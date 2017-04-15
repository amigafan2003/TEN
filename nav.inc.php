<nav id="menu">
	<header class="major">
		<h2>Menu</h2>
	</header>
	<ul>
		<li><a href="index.php">Home</a>
		</li>
		<?php 
			if(isset($_SESSION['user_id'])) { ?>
				<li><span class="opener">Feeds
					</span>
					<ul>
						<li><a href="feeds.php">All feeds</a>
						</li>
						<li><a href="rated_feeds.php">Feeds by rating</a>
						</li>						
						<li><a href="trending.php">Trending feeds</a>
						</li>
						<li><a href="most_subbed_feeds.php">Most subscribed feeds</a>
						</li>
						<li><a href="Most_commented_feeds.php">Most commented feeds</a>
						</li>
					</ul>
				</li>
				<li><a href="mysubscriptions.php">My Subscriptions</a>
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