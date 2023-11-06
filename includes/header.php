<?php
global $userIsLoggedIn,$currentNav;
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title>PANTRYPAGE</title>
	
	<link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css"
      rel="stylesheet"
    />

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9"
      crossorigin="anonymous"
    />
    <link
      href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css"
      rel="stylesheet"
    />
	<link href="css/styles.css" rel="stylesheet"> 
	

</head>

<body >

<nav class="navbar navbar-expand-md navbar-dark fixed-top " style="background-color: rgba(242, 110, 86, 255);">
    <div class="container-fluid ">

    <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasExample"
          >
            <span class="navbar-toggler-icon"></span>
          </button>

        <a class="navbar-brand" href="#">
            <span class="bi bi-flower3"></span>
            PANTRYPAGE
        </a>

        <div class="d-flex flex-grow-1">
            <form class="form-inline w-100">
                <div class="input-group w-100">
                    <input
                        class="form-control"
                        type="search"
                        placeholder="Search for a recipe?"
                        aria-label="Search"
                    />
                    <button class="btn btn-outline-success search-btn" type="submit">
                        🔍
                    </button>
                </div>
            </form>
        </div>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
                    <li class="<?php if ($currentNav == "home") echo "active"; ?> nav-item">
						<a class="nav-link" href="index.php">Home</a>
					</li>
           
				<ul class="navbar-nav ms-auto">
                    <li class="<?php if ($currentNav == "recent") echo "active"; ?> nav-item">
						<a class="nav-link" href="aboutus.php">About</a>
					</li>
					<?php if($userIsLoggedIn){ ?>
						<li class="nav-item <?php if ($currentNav == "add") echo "active"; ?> ">
							<a class="nav-link" href="profilesetting.php?user=<?=$_SESSION['user_id'];?>"><?=$_SESSION['userName']?></a>
						</li>
						<li>
							<a class="nav-link" href="logout.php">Logout</a>
						</li>
					<?php } else { ?>
						<li class="nav-item <?php if ($currentNav == "login") echo "active"; ?> ">
							<a class="nav-link" href="login.php">Login</a>
						</li>
					<?php } ?>
				</ul>
			</div>

		</div>

	</nav>
  <!-- ... Offcanvas Menu ... -->
  <div
      class="offcanvas offcanvas-start  custom-mt "
      style="background-color: rgba(242, 110, 86, 255);"
      tabindex="-1"
      id="offcanvasExample"
    >
      <div class="offcanvas-header">
        <h5 class="offcanvas-title">Tango</h5>
        <button
          type="button"
          class="btn-close text-reset text-bg-light"
          data-bs-dismiss="offcanvas"
          aria-label="Close"
        ></button>
      </div>
      <div class="offcanvas-body">
        <!-- Offcanvas content: link, button, menu -->
        <ul class="navbar-nav">
          <li class="nav-item">
            <a
              class="nav-link login-btn"
              href="#"
              <a
              class="nav-link login-btn"
              href="#"
              data-bs-toggle="modal"
              data-bs-target="#loginModal"
              >Login</a
            >
          </li>
          <li class="nav-item"><a class="nav-link" href="#">Signup</a></li>
          <hr />
          <li class="nav-item"><a class="nav-link" href="#">About</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
        </ul>
      </div>
    </div>