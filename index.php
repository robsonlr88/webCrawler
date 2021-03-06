<?php
include('crawler.php');
$url="";
$pages="";
$images=0;
$internalCount=0;
$pageLoad=0;
$wordCount=0;
$titleLength=0;
$pageCrawled=1;
if(isset($_POST["pages"]) && isset($_POST["url"]))
{
  $url = parse_url($_POST["url"], PHP_URL_SCHEME) === null ? 'http://' . $_POST["url"] : $_POST["url"];
	$pages = $_POST["pages"];
	$crawler = new Crawler($url,$pages); 
	$crawler->run(); 
	$visited = $crawler->getResult();
	$pageCrawled = count($visited);
	foreach($visited as $visit)
	{
		$url = $visit['url'];
		$images += $visit['images'];
		$internalCount += $visit['internal'];
		$wordCount += $visit['words'];
		$titleLength += $visit['title_length'];			
		$pageLoad +=$visit['load'];

	}
}
?>


<!DOCTYPE html>
<html>
<head>
  <title>PHP Crawler</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body class="bg-light">
  <div class="container p-3">
    <div class="row p-5 bg-white border rounded mb-4">
      <h3 class="text-center">PHP Crawler</h3>
      <form method="POST" action="">
        <div class="row pt-4 p-3">
          <div class="text-center">
						<input type="text" class="p-3 border rounded text-center" id="url" name="url" value="<?=$url?>" placeholder="URL">
					</div>
          <h5 class="p-3 text-center">How many pages would you like to crawl?</h5>
          <div class="text-center ">
            <input class="p-3 border rounded text-center" type="number" id="pages" name="pages" placeholder="Between 4 - 6 pages" min=4 max=6 style="width: 250px">
            <button type="submit" class="p-3 btn btn-primary">Crawl</button>
          </div>
        </div>
      </form>
    </div>


    <div class="row p-5 bg-white border rounded ">
      <div class="text-center">
          Number of pages crawled: <?= $pageCrawled ?>
      </div>
      <div class="text-center">        
          Number of a unique images: <?= $images ?>
      </div>
      <div class="text-center">
          Number of unique internal links: <?= $internalCount ?>
      </div>
      <div class="text-center">
          Average page load in seconds: <?= round($pageLoad/$pageCrawled,2) ?>
      </div>
      <div class="text-center">
          Average word count: <?= round($wordCount/$pageCrawled,2) ?>
      </div>
      <div class="text-center">
          Average title length: <?= round($titleLength/$pageCrawled,2) ?>
      </div>


      <table class="table">
        <thead class="thead-dark">
          <tr>
            <th scope="col" style="width: 50%;">Name</th>
            <th scope="col" style="width: 30%;" class="p-3">HTTP code</th>
          </tr>
        </thead>
        <tbody>
        <?php if(isset($_POST["pages"])){
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
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>
</html>