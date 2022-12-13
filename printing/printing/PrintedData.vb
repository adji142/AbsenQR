Imports Dapper
Imports printing.BOT
Imports System.Net
Imports System.IO
Imports Newtonsoft.Json
Public Class Printed
    Public Property NoTransaksi As String
    Public Property Createdby As String
    Public Property T_Bayar As Double
    Public Property T_Kembali As Double
    Public Property Qty As Integer
    Public Property Harga As Double
    Public Property Disc As Double
    Public Property NamaPerusahaan As String
    Public Property Alamat1 As String
    Public Property NoTlp As String
    Public Property Article As String
End Class
Public Class PrintedData
    Private _DBConnection As New DBConnection
    Public Function getPrintingdoc() As DataSet
        Dim DS As New DataSet
        Dim SQL = "select * from printinglog where Printed = 0 ORDER BY Sumitedat DESC LIMIT 1"
        Try
            Using DBX As IDbConnection = _DBConnection.Connection
                Dim CMD As New MySql.Data.MySqlClient.MySqlCommand(SQL, DBX)
                Dim DA As New MySql.Data.MySqlClient.MySqlDataAdapter

                DA.SelectCommand = CMD
                DA.Fill(DS, "View")
            End Using
        Catch ex As Exception
            Console.WriteLine(ex.Message)
        End Try
        getPrintingdoc = DS
    End Function
    Public Function UpdateFlag(ByVal NoTransaksi As String) As Boolean
        Dim SQL As String

        SQL = "UPDATE printinglog SET Printed = 1 " +
              "WHERE NoTransaksi = @NoTransaksi "

        Using DBX As IDbConnection = _DBConnection.Connection()
            Try
                DBX.Execute(SQL, New With {.NoTransaksi = NoTransaksi})
                UpdateFlag = True
            Catch ex As Exception
                UpdateFlag = False
            End Try
        End Using
    End Function
    Public Function GetDataAPI() As DataSet
        Dim DT As New penjualan.PjlPrintDataTable
        Dim newRow As penjualan.PjlPrintRow
        Dim data As New DataSet
        Dim URI As String = "opos.dawnstore.id/getprinted"
        Dim hwr As HttpWebRequest
        Dim respornseserver = ""

        hwr = WebRequest.Create("http://" + URI)

        Try
            Dim Wr As WebResponse

            Wr = hwr.GetResponse
            Console.WriteLine(CType(Wr, HttpWebResponse).StatusDescription)

            Using x As Stream = Wr.GetResponseStream
                Dim reader As New StreamReader(x)
                respornseserver = reader.ReadToEnd()

                Console.WriteLine(respornseserver)
            End Using

            Dim datax = JsonConvert.DeserializeObject(Of List(Of Printed))(respornseserver)

            If datax.Count > 0 Then
                For index As Integer = 0 To datax.Count - 1
                    newRow = DT.NewRow
                    newRow.NoTransaksi = datax(index).NoTransaksi
                    newRow.Createdby = datax(index).Createdby
                    newRow.T_Bayar = datax(index).T_Bayar
                    newRow.T_Kembali = datax(index).T_Kembali
                    newRow.Qty = datax(index).Qty
                    newRow.Harga = datax(index).Harga
                    newRow.Disc = datax(index).Disc
                    newRow.NamaPerusahaan = datax(index).NamaPerusahaan
                    newRow.Alamat1 = datax(index).Alamat1
                    newRow.NoTlp = datax(index).NoTlp
                    newRow.Article = datax(index).Article

                    DT.Rows.Add(newRow)
                Next
            End If

            data.Merge(DT)

        Catch ex As Exception
            MessageBox.Show(ex.Message)
        End Try
        Return data
    End Function
End Class
