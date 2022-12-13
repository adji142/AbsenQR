Imports CrystalDecisions.CrystalReports.Engine
Imports CrystalDecisions.Shared
Imports printing.BOT
Public Class ExportGrid

    Dim _Date As Date = Now.Date
    Public Function _Create(ByVal NoTransaksi As String)
        Try
            Dim Server As New ConnectionInfo
            'Dim FileName As String = "Notulen" & Now.Ticks.ToString & "-" & Counting & ".PDF"

            Dim DBX As Object = New DBConnection().ConnectionSetting()
            'Setting Koneksi Database
            With Server
                .ServerName = "DRIVER={MySQL ODBC 5.3 ANSI Driver};SERVER=" + DBX.Server + "; PORT = " + DBX.Port.ToString + "; "
                .DatabaseName = "dbpos"
                .UserID = DBX.UserID
                .Password = DBX.Password
            End With

            '-----------------------------------------------------------------------------------------

            Dim RPTObject As New ReportDocument
            RPTObject.Load(System.AppDomain.CurrentDomain.BaseDirectory() + "\Report1.rpt")

            Dim DataTable As Table
            For Each DataTable In RPTObject.Database.Tables
                DataTable.LogOnInfo.ConnectionInfo = Server
                DataTable.ApplyLogOnInfo(DataTable.LogOnInfo)
            Next

            RPTObject.ParameterFields("NoTransaksi").CurrentValues.AddValue(NoTransaksi)

            'RPTObject.DataDefinition.FormulaFields("RAPAT").Text = "'" + Rapat + "'"
            'RPTObject.DataDefinition.FormulaFields("TANGGAL").Text = "'" + Tgl + "'"
            'RPTObject.DataDefinition.FormulaFields("Periode").Text = "'" + GetPeriodDescription(Periode).ToUpper + "'"
            'RPTObject.DataDefinition.FormulaFields("UserID").Text = "'" + UCase(ActiveSession.KodeUser) + "'"
            'RPTObject.ExportToDisk(ExportFormatType.PortableDocFormat, System.AppDomain.CurrentDomain.BaseDirectory() & "/" & FileName)
            RPTObject.PrintToPrinter(1, False, 1, 1)
            'Dim X As New Form1()
            'X.CrystalReportViewer1.ReportSource = RPTObject
            'X.CrystalReportViewer1.Refresh()

            _Create = "Printed"
        Catch ex As Exception
            _Create = "ERROR " + ex.Message
        End Try

    End Function
End Class
