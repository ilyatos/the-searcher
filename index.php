<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <title>Welcome to Searcher</title>

    <!-----CSS Styles----->
    <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>
<div class="wrapper indexPage">
    <div class="mainSection">
        <div class="logoContainer">
            <img src="assets/img/searcher_google.png" sizes="100 100" alt="Searcher">
        </div>
        <div class="searchContainer">
            <form action="search.php" method="get">
                <input class="searchBox" type="text" name="term" placeholder="Enter a search term..." required>
                <input class="searchButton" type="submit" value="Search">
            </form>
        </div>
    </div>
</div>
</body>
</html>