<nav id="menu">
	<header class="major">
		<h2>Menu</h2>
	</header>
	<ul>
		<li><a href="index.php">Home</a>
		</li>
		<?php 
			if(isset($_SESSION['user_id'])) { ?>
				<li><a href="feeds.php">Feeds</a>
				</li>
				<li><a href="mysubscriptions.php">My Subscriptions</a>
				</li>
				<li>
					<span class="opener">Administration</span>
					<ul>
						<li><a href="admin_feeds.php">Manage RSS feeds</a>
						</li>
					</ul>
				</li>
				<li><a href="index.php?#contact">Contact Us</a>
				</li>
				<li><a href="logout.php">Logout</a>
				</li>
			<?php 
			} else {
			?>	<li><a href="index.php?#about">About</a>
				<li><a href="index.php?#contact">Contact Us</a>
				</li>
				<li><a href="login.php">Login</a>
				</li>
		<?php } ?>
	</ul>
</nav>