<nav id="menu">
	<header class="major">
		<h2>Menu</h2>
	</header>
	<ul>
		<li><a href="index.php">Home</a>
		</li>
		<?php 
			if(isset($_SESSION['user_id'])) { ?>
				<li><a href="feeds.php?userid=<?php echo $_SESSION[user_id] ?>">Feeds</a>
				</li>
				<li><a href="mysubscriptions.php?userid=<?php echo $_SESSION[user_id] ?>">My Subscriptions</a>
				</li>
				<?php
					if ( isset( $_SESSION[ "u_level" ] ) ) {
						if ( $_SESSION[ "u_level" ] == "admin" ) {
						?>
							<li>
								<span class="opener">Administration</span>
								<ul>
									<li><a href="admin_feeds.php">Manage RSS feeds</a>
									</li>
								</ul>
							</li>
						<?php	
						}
					}
				?>
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