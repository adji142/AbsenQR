Imports System.IO
Imports System.Threading
Imports System.Configuration
Imports CrystalDecisions.CrystalReports.Engine
Imports CrystalDecisions.Shared

Public Class Service1

    Protected Overrides Sub OnStart(ByVal args() As String)
        ' Add code here to start your service. This method should set things
        ' in motion so your service can do its work.
        Me.WriteToFile("Simple Service started at " + DateTime.Now.ToString("dd/MM/yyyy hh:mm:ss tt"))
        Me.ScheduleService()
    End Sub

    Protected Overrides Sub OnStop()
        ' Add code here to perform any tear-down necessary to stop your service.
        Me.WriteToFile("Simple Service stopped at " + DateTime.Now.ToString("dd/MM/yyyy hh:mm:ss tt"))
        Schedular.Dispose()
    End Sub
    Private Schedular As Timer

    Public Sub ScheduleService()
        Try
            Schedular = New Timer(New TimerCallback(AddressOf SchedularCallback))
            Dim mode As String = My.Settings.Mode.ToUpper()
            Me.WriteToFile((Convert.ToString("Simple Service Mode: ") & mode) + " {0}")

            'Set the Default Time.
            Dim scheduledTime As DateTime = DateTime.MinValue

            If mode.ToUpper() = "INTERVAL" Then
                'Get the Interval in Minutes from AppSettings.
                Dim intervalSec As Integer = Convert.ToInt32(My.Settings.Interval)

                'Set the Scheduled Time by adding the Interval to Current Time.
                scheduledTime = DateTime.Now.AddSeconds(intervalSec)
                If DateTime.Now > scheduledTime Then
                    'If Scheduled Time is passed set Schedule for the next Interval.
                    scheduledTime = scheduledTime.AddMinutes(intervalSec)
                End If
            End If

            Dim timeSpan As TimeSpan = scheduledTime.Subtract(DateTime.Now)
            Dim schedule As String = String.Format("{0} day(s) {1} hour(s) {2} minute(s) {3} seconds(s)", timeSpan.Days, timeSpan.Hours, timeSpan.Minutes, timeSpan.Seconds)

            Me.WriteToFile((Convert.ToString("Simple Service scheduled to run after: ") & schedule) + " {0}")

            'Sample writing
            'For x = 0 To 50
            '    Me.WriteToFile("urutan ke " & x)
            'Next

            Me.print()
            'Get the difference in Minutes between the Scheduled and Current Time.
            Dim dueTime As Integer = Convert.ToInt32(timeSpan.TotalMilliseconds)

            'Change the Timer's Due Time.
            Schedular.Change(dueTime, Timeout.Infinite)
        Catch ex As Exception
            WriteToFile("Simple Service Error on: {0} " + ex.Message + ex.StackTrace)

            'Stop the Windows Service.
            Using serviceController As New System.ServiceProcess.ServiceController("SimpleService")
                serviceController.[Stop]()
            End Using
        End Try
    End Sub
    Private Sub SchedularCallback(e As Object)
        Me.WriteToFile("Simple Service Log: " + DateTime.Now.ToString("dd/MM/yyyy hh:mm:ss tt"))
        Me.ScheduleService()
    End Sub
    Private Sub WriteToFile(text As String)
        Dim path As String = "C:\log\ServiceLog-" + DateTime.Now.ToString("dd-MM-yyyy") + ".txt"
        Dim SaveDirectory As String = "C:\log"

        Dim Filename As String = System.IO.Path.GetFileName(path)
        Dim SavePath As String = System.IO.Path.Combine(SaveDirectory, Filename)
        If Not System.IO.File.Exists(SavePath) Then
            'The file exists
            File.Create(path).Dispose()
        End If
        Using writer As New StreamWriter(path, True)
            writer.WriteLine(String.Format(text, DateTime.Now.ToString("dd/MM/yyyy hh:mm:ss tt")))
            writer.Close()
        End Using

        'SendBroadcast()
    End Sub
    Public Sub print()
        'Dim ex As New ExportGrid()
        'Dim data As New PrintedData
        'Dim ds As New DataSet
        'ds = data.getPrintingdoc()
        'If ds.Tables(0).Rows.Count > 0 Then
        '    Me.WriteToFile("Printing: " + DateTime.Now.ToString("dd/MM/yyyy hh:mm:ss tt"))
        '    ex._Create(ds.Tables(0).Rows(0)("NoTransaksi").ToString)
        '    data.UpdateFlag(ds.Tables(0).Rows(0)("NoTransaksi").ToString)
        'Else
        '    Me.WriteToFile("Nothing to print: " + DateTime.Now.ToString("dd/MM/yyyy hh:mm:ss tt"))
        'End If

        Dim ex As New ExportGrid()
        Dim data As New PrintedData
        Dim ds As New DataSet
        ds = data.GetDataAPI()

        If ds.Tables(0).Rows.Count > 0 Then
            Me.WriteToFile("Printing: " + DateTime.Now.ToString("dd/MM/yyyy hh:mm:ss tt"))
            Dim RPTObject As New ReportDocument
            RPTObject.Load(System.AppDomain.CurrentDomain.BaseDirectory() + "\Report3.rpt")
            'RPTObject.ParameterFields("NoTransaksi").CurrentValues.AddValue(ds.Tables(0).Rows(0)("NoTransaksi"))
            RPTObject.SetDataSource(ds)

            RPTObject.VerifyDatabase()
            RPTObject.Refresh()
            'RPTObject.ExportToDisk(ExportFormatType.PortableDocFormat, System.AppDomain.CurrentDomain.BaseDirectory() & "/xxx2.pdf")
            Dim pDoc As New System.Drawing.Printing.PrintDocument
            Dim PrintLayout As New CrystalDecisions.Shared.PrintLayoutSettings
            Dim printerSettings As New System.Drawing.Printing.PrinterSettings

            printerSettings.PrinterName = "58mm Series Printer"
            Dim pSettings As System.Drawing.Printing.PageSettings = New System.Drawing.Printing.PageSettings(printerSettings)
            RPTObject.PrintOptions.DissociatePageSizeAndPrinterPaperSize = True
            RPTObject.PrintOptions.PrinterDuplex = PrinterDuplex.Simplex


            RPTObject.PrintToPrinter(printerSettings, pSettings, False)
            'RPTObject.PrintToPrinter(1, False, 1, 1)

        Else
            Me.WriteToFile("Nothing to print: " + DateTime.Now.ToString("dd/MM/yyyy hh:mm:ss tt"))
        End If
    End Sub
End Class