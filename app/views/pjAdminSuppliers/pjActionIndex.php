<style>
	.kpi-row{
		display: flex;
		flex-wrap: wrap;
		gap: 15px;
	}
	.kpi-col{flex: 1 1 200px;}
	.kpi-card{
		background: #fff;
	  border-radius: 12px;
	  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
	  padding: 20px;
	  transition: transform 0.2s, box-shadow 0.2s;
	}
	.kpi-top {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}
.kpi-total-rides i {
    color: #3498db;
}
.kpi-total-revenue i {
    color: #2ecc71;
}
.kpi-top i {
    font-size: 28px;
    
}
.kpi-card h4 {
     font-size: 14px; 
    font-weight: 600;
    margin: 0;
    color: #555;
}
.kpi-completed-rides i {
    color: #1abc9c;
}
.kpi-total-drivers i {
    color: #9b59b6;
}
.kpi-past-rides i {
    color: #e67e22;
}
.kpi-upcoming-rides i {
    color: #e74c3c;
}
</style>

<div id="dashboardContent">
	<div class="kpi-row">
			<div class="kpi-col">
				<div class="kpi-card  kpi-total-rides">
					<div class="kpi-top">
						<i class="fa fa-taxi"></i>
						<h4><?php __('plugin_base_lbl_total_no_rides');?></h4>
					</div>
					<h2><?php echo $tpl['avail_rides']; ?></h2>
				</div>
			</div>
			<div class="kpi-col">
				<div class="kpi-card kpi-completed-rides">
					<div class="kpi-top">
						<i class="fa fa-check-circle"></i>
						<h4><?php __('plugin_base_lbl_completed_rides');?></h4>
					</div>
					<h2>0</h2>
				</div>
			</div>
			<div class="kpi-col">
				<div class="kpi-card kpi-total-revenue">
					<div class="kpi-top">
						<i class="fa fa-money"></i>
						<h4><?php __('plugin_base_lbl_total_no_rides');?></h4>
					</div>
					<h2>0</h2>
				</div>
			</div>
			<div class="kpi-col">
				<div class="kpi-card  kpi-upcoming-rides">
					<div class="kpi-top">
						<i class="fa fa-calendar-check-o"></i>
						<h4><?php __('plugin_base_menu_upcoming_rides');?></h4>
					</div>
					<h2>0</h2>
				</div>
			</div>
			<!-- <div class="kpi-col">
				<div class="kpi-card kpi-past-rides">
					<div class="kpi-top">
						<i class="fa fa-history"></i>
						<h4>Past rides</h4>
					</div>
					<h2>0</h2>
				</div>
			</div> -->
			<div class="kpi-col">
				<div class="kpi-card kpi-total-drivers">
					<div class="kpi-top">
						<i class="fa fa-users"></i>
						<h4><?php __('plugin_base_lbl_total_drivers');?></h4>
					</div>
					<h2><?php echo $tpl['total_drivers']; ?></h2>
				</div>
			</div>
		</div>
</div>