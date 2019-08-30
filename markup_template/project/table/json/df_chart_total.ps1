$url = "http://work.designfever.com/project/table/json/df_chart_total.json.php?rn=2"

$request = [System.Net.WebRequest]::Create($url)

$response = $request.GetResponse()

$response.Close()