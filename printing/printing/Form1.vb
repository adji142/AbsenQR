Imports CrystalDecisions.CrystalReports.Engine
Imports CrystalDecisions.Shared
Imports System.IO

Public Class Form1



    Private Sub Button1_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles Button1.Click
        Dim ex As New ExportGrid()
        Dim data As New PrintedData
        Dim ds As New DataSet
        ds = data.GetDataAPI()

        Dim RPTObject As New ReportDocument
        RPTObject.Load(System.AppDomain.CurrentDomain.BaseDirectory() + "\Report3.rpt")
        'RPTObject.ParameterFields("NoTransaksi").CurrentValues.AddValue(ds.Tables(0).Rows(0)("NoTransaksi"))
        RPTObject.SetDataSource(ds)

        RPTObject.VerifyDatabase()
        RPTObject.Refresh()
        Dim pDoc As New System.Drawing.Printing.PrintDocument
        Dim PrintLayout As New CrystalDecisions.Shared.PrintLayoutSettings
        Dim printerSettings As New System.Drawing.Printing.PrinterSettings

        printerSettings.PrinterName = "58mm Series Printer"
        Dim pSettings As System.Drawing.Printing.PageSettings = New System.Drawing.Printing.PageSettings(printerSettings)
        RPTObject.PrintOptions.DissociatePageSizeAndPrinterPaperSize = True
        RPTObject.PrintOptions.PrinterDuplex = PrinterDuplex.Simplex
        'RPTObject.ExportToDisk(ExportFormatType.PortableDocFormat, System.AppDomain.CurrentDomain.BaseDirectory() & "/xxx2.pdf")
        RPTObject.PrintToPrinter(printerSettings, pSettings, False, PrintLayout)
    End Sub



    Private Sub Form1_Load(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles MyBase.Load

        'RichTextBox1.LoadFile("D:\Text.txt", RichTextBoxStreamType.PlainText)

        'PictureBox1.Image = Image.FromFile("E:\VBproject\Gap.bmp")

    End Sub



    Private Sub PrintDocument1_PrintPage(ByVal sender As System.Object, ByVal e As System.Drawing.Printing.PrintPageEventArgs)



        'Dim newImage As Image = Image.FromFile("E:\VBproject\Gap.bmp")


        'e.Graphics.DrawImage(newImage, 100, 100)

        ' You also can reference an image to PictureBox1.Image.



        'Dim txtReader As System.IO.StreamReader = New System.IO.StreamReader("D:\Text.txt")

        'Dim textOfFile As String = txtReader.ReadToEnd

        'txtReader.Close()

        e.Graphics.DrawString("Test Text", New Font("IDAutomationHC39M", 16), Brushes.Black, 50, 100)

        ' You also can reference some text to RichTextBox1.Text, etc.



    End Sub

    Private Sub Button2_Click(sender As Object, e As EventArgs) Handles Button2.Click
        Dim path As String = "C:\log\ServiceLog-" + DateTime.Now.ToString("dd-MM-yyyy") + ".txt"
        Dim SaveDirectory As String = "C:\log"

        Dim Filename As String = System.IO.Path.GetFileName(path)
        Dim SavePath As String = System.IO.Path.Combine(SaveDirectory, Filename)
        If Not System.IO.File.Exists(SavePath) Then
            'The file exists
            File.Create(path).Dispose()
        End If
        Using writer As New StreamWriter(path, True)
            writer.WriteLine(String.Format(Text, DateTime.Now.ToString("dd/MM/yyyy hh:mm:ss tt")))
            writer.Close()
        End Using
    End Sub

End Class