<?php
include('crawler.php');
$url="https://agencyanalytics.com";
$pages="";
if($_POST["pages"]!="")
{
	$imageCount=0;
  $internalCount=0;
  $externalCount=0;
  $pageLoad=0;
  $wordCount=0;
  $titleLength=0;
	//$url = parse_url($_POST["url"], PHP_URL_SCHEME) === null ? 'http://' . $_POST["url"] : $_POST["url"];
	//$pages = 4;
	$pages = $_POST["pages"];
	$crawler = new Crawler($url,$pages); // create an object of Crawler class
	$crawler->run(); // execute the code
	$visited = $crawler->getResult(); // fetch the result into an array
	$pageCrawled = count($visited);
	foreach($visited as $visit)
	{
		//var_dump($visit['title_length']);
		$url = $visit['url'];
		$imageCount += $visit['images'];
		$internalCount += $visit['internal'];
		$externalCount += $visit['external'];
		$wordCount += $visit['words'];
		$titleLength += $visit['title_length'];			
		$pageLoad +=$visit['load'];
	}
}
?>


<!DOCTYPE html>
<html>
<head>
  <title>PHP Crawler - Agency Analytics</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

</head>
<body class="bg-light">
  <div class="container p-3">
    <div class="row p-5 bg-white border rounded mb-4">
      <h3 class="text-center">PHP Crawler on agencyanalytics.com</h3>
      <form method="POST" action="">
        <div class="row pt-4 p-3">
          <div class="form-check form-switch">
            <h5 class=" p-3 text-center">How many pages would you like to crawl?</h5>
            <div class="text-center ">
              <input class=" p-3 border rounded text-center" type="number" id="pages" name="pages" placeholder="Between 4 - 6 pages" min=4 max=6>
              <button type="submit" class="p-3 btn btn-primary">Crawl</button>
            </div>
          </div>
        </div>
      </form>
    </div>


      <div class="row p-5 bg-white border rounded ">
        <div>
            Number of pages crawled: <?= $pageCrawled ?>
        <div>        
            Number of a unique images: 
        </div>
        <div>
            Number of unique internal links:
        </div>
        <div>
            Number of unique external links: 
        </div>
        <div>
            Average page load in seconds:
        </div>
        <div>
            Average word count:
        </div>
        <div>
            Average title length:
        </div>
      </div>

    <table class="table">
      <thead class="thead-dark">
        <tr>
          <th scope="col" style="width: 50%;">Name</th>
          <th scope="col" style="width: 30%;" class="p-3">HTTP code</th>
        </tr>
      </thead>
      <tbody>
      <?php if($_POST["pages"]!=""){
        foreach($visited as $visit){?>
        <tr>
          <td style="width: 50%;" scope="row">
            <div>
              <h5>
              <?=$visit['url']?>
              </h5>
            </div>
          </td>
          <td style="width: 30%;" scope="row" class="p-2">
            <?=$visit['code']?>
          </td>
        </tr>
        <?php 
        }
        } 
        ?>	
      </tbody>
    </table>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>



</body>
</html>