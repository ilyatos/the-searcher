<?php
include 'db_connetcion.php';


if(!empty($_GET['term'])) {
    $term = $_GET['term'];
} else {
    exit('Enter a search term');
}

$type = !empty($_GET['type']) ? $_GET['type'] : 'sites';

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <title>Welcome to Searcher</title>

    <!-----CSS Styles----->
    <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>
<div class="wrapper">
    <div class="header">
        <div class="headerContent">
            <div class="logoContainer">
                <a href="index.php">
                    <img src="assets/img/searcher_google.png" sizes="100 100" alt="Searcher">
                </a>
            </div>
            <div class="searchContainer">
                <form action="search.php" method="get">
                    <div class="searchBarContainer">
                        <input class="searchBox" type="text" name="term" value="<?php echo $term; ?>" required>
                        <button class="searchButton">
                            <img src="assets/img/icons/search-button.png">
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="tabsContainer">
            <ul class="tabList">
                <li class="<?php echo $type == 'sites' ? 'active' : ''; ?>">
                    <a href='<?php echo "search.php?term=$term&type=sites"; ?>'>
                        Sites
                    </a>
                </li>
                <li class="<?php echo $type == 'images' ? 'active' : ''; ?>">
                    <a href='<?php echo "search.php?term=$term&type=images"; ?>'>
                        Images
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
</body>
</html>