<?php $this->load->view('Administrator/dashboard_style'); ?>
<?php

$userID =  $this->session->userdata('userId');
$CheckSuperAdmin = $this->db->where('UserType', 'm')->where('User_SlNo', $userID)->get('tbl_user')->row();

$CheckAdmin = $this->db->where('UserType', 'a')->where('User_SlNo', $userID)->get('tbl_user')->row();

$userAccessQuery = $this->db->where('user_id', $userID)->get('tbl_user_access');
$access = [];
if ($userAccessQuery->num_rows() != 0) {
	$userAccess = $userAccessQuery->row();
	$access = json_decode($userAccess->access);
}

$companyInfo = $this->db->query("select * from tbl_company c order by c.Company_SlNo desc limit 1")->row();


$panel = $this->session->userdata('panel');
if ($panel == 'dashboard' or $panel == '') { ?>
	<div class="row">
		<div class="col-md-12 col-xs-12">
			<!-- Header Logo -->
			<div class="col-md-12 header" style="height: 130px;box-shadow:none;">
				<img src="<?php echo base_url(); ?>assets/images/headerbg.jpg" style="border-radius: 20px;border: 1px solid #007ebb;box-shadow: 0px 5px 0px 0px #007ebb;" class="img img-responsive center-block">
			</div>

			<div class="col-md-10 col-md-offset-1" style="padding-top: 10px;">
				<div class="col-md-3 col-xs-6 section4">
					<div class="col-md-12 section122">
						<a href="<?php echo base_url(); ?>panel/SalesPanel">
							<div class="logo">
								<i class="ri-store-2-line"></i>
							</div>
							<div class="textModule">
								Manage Sales
							</div>
						</a>
					</div>
				</div>

				<?php if ($this->session->userdata('BRANCHid') == 1 && (isset($CheckSuperAdmin) || isset($CheckAdmin))) : ?>
					<div class="col-md-3 col-xs-6 section4">
						<div class="col-md-12 section122">
							<a href="<?php echo base_url(); ?>panel/InventoryPanel">
								<div class="logo">
									<i class="ri-briefcase-line"></i>
								</div>
								<div class="textModule">
									Manage Inventory
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<!-- module/AccountsModule -->
				<div class="col-md-3 col-xs-6 section4">
					<div class="col-md-12 section122">
						<a href="<?php echo base_url(); ?>panel/AccountsPanel">
							<div class="logo">
								<i class="ri-wallet-3-fill"></i>
							</div>
							<div class="textModule">
								Manage Accounts
							</div>
						</a>
					</div>
				</div>

				<!-- module/HRMPanel -->
				<div class="col-md-3 col-xs-6 section4">
					<div class="col-md-12 section122">
						<a href="<?php echo base_url(); ?>panel/HRMPanel">
							<div class="logo">
								<i class="ri-team-line"></i>
							</div>
							<div class="textModule">
								Manage HRM
							</div>
						</a>
					</div>
				</div>

				<!-- module/ReportsPanel -->
				<div class="col-md-3 col-xs-6 section4">
					<div class="col-md-12 section122">
						<a href="<?php echo base_url(); ?>panel/ReportsPanel">
							<div class="logo">
								<i class="ri-honour-line"></i>
							</div>
							<div class="textModule">
								Manage Reports
							</div>
						</a>
					</div>
				</div>

				<div class="col-md-3 col-xs-6 section4">
					<div class="col-md-12 section122">
						<a href="<?php echo base_url(); ?>panel/Administration">
							<div class="logo">
								<i class="ri-home-gear-line"></i>
							</div>
							<div class="textModule">
								Administration
							</div>
						</a>
					</div>
				</div>

				<div class="col-md-3 col-xs-6 section4">
					<div class="col-md-12 section122">
						<a href="<?php echo base_url(); ?>graph">
							<div class="logo">
								<i class="ri-bar-chart-box-ai-fill"></i>
							</div>
							<div class="textModule">
								Business View
							</div>
						</a>
					</div>
				</div>


				<div class="col-md-3 col-xs-6 section4">
					<div class="col-md-12 section122">
						<a href="<?php echo base_url(); ?>Login/logout">
							<div class="logo">
								<i class="ri-logout-circle-r-line"></i>
							</div>
							<div class="textModule">
								LogOut
							</div>
						</a>
					</div>
				</div>
			</div>
			<!-- PAGE CONTENT ENDS -->
		</div><!-- /.col -->
	</div>

<?php } elseif ($panel == 'Administration') { ?>

	<div class="row">
		<div class="col-md-12 col-xs-12">
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<!-- Header Logo -->
				<div class="col-md-12 header">
					<h3> Manage Administration </h3>
				</div>
				<?php if (array_search("product", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>product">
								<div class="logo">
									<i class="menu-icon ri-product-hunt-line"></i>
								</div>
								<div class="textModule">
									Product Entry
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("productlist", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>productlist">
								<div class="logo">
									<i class="ri-list-radio"></i>
								</div>
								<div class="textModule">
									Product list
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("product_ledger", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>product_ledger">
								<div class="logo">
									<i class="ri-todo-line"></i>
								</div>
								<div class="textModule">
									Product Ledger
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<!-- <?php if (array_search("campaign_products", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>campaign_products">
								<div class="logo">
									<i class="menu-icon fa fa-plus-circle"></i>
								</div>
								<div class="textModule">
									Campaign Products
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("campaignlist", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>campaignlist">
								<div class="logo">
									<i class="menu-icon fa fa-list-ul"></i>
								</div>
								<div class="textModule">
									Campaign List
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?> -->

				<?php if (array_search("damageEntry", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>damageEntry">
								<div class="logo">
									<i class="ri-file-damage-line"></i>
								</div>
								<div class="textModule">
									Damage Entry
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("damageList", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>damageList">
								<div class="logo">
									<i class="ri-list-radio"></i>
								</div>
								<div class="textModule">
									Damage List
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<!-- <?php if (array_search("product_transfer", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>product_transfer">
								<div class="logo">
									<i class="menu-icon fa fa-exchange"></i>
								</div>
								<div class="textModule">
									Product Transfer
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("transfer_list", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>transfer_list">
								<div class="logo">
									<i class="menu-icon fa fa-list"></i>
								</div>
								<div class="textModule">
									Transfer List
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("received_list", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>received_list">
								<div class="logo">
									<i class="menu-icon fa fa-list"></i>
								</div>
								<div class="textModule">
									Received List
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?> -->
				<?php if (array_search("customer", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>customer">
								<div class="logo">
									<i class="ri-user-2-line"></i>
								</div>
								<div class="textModule">
									Customer Entry
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("supplier", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>supplier">
								<div class="logo">
									<i class="ri-user-3-line"></i>
								</div>
								<div class="textModule">
									Supplier Entry
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("category", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>category">
								<div class="logo">
									<i class="ri-apps-2-add-fill"></i>
								</div>
								<div class="textModule">
									Category entry
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("unit", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>unit">
								<div class="logo">
									<i class="ri-layout-grid-fill"></i>
								</div>
								<div class="textModule">
									Unit Entry
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("area", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>area">
								<div class="logo">
									<i class="ri-global-line"></i>
								</div>
								<div class="textModule">
									Add Area
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>


				<!-- <?php if ($this->session->userdata('BRANCHid') == 1 && (isset($CheckSuperAdmin) || isset($CheckAdmin))) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>branch">
								<div class="logo">
									<i class="menu-icon fa fa-bank"></i>
								</div>
								<div class="textModule">
									Branch Entry
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?> -->

				<?php if ($this->session->userdata('BRANCHid') == 1 && (isset($CheckSuperAdmin) || isset($CheckAdmin))) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>companyProfile">
								<div class="logo">
									<i class="menu-icon ri-home-gear-line"></i>
								</div>
								<div class="textModule">
									Company Profile
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (isset($CheckSuperAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>user">
								<div class="logo">
									<i class="menu-icon ri-user-settings-line"></i>
								</div>
								<div class="textModule">
									Create User
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<!-- PAGE CONTENT ENDS -->
		</div><!-- /.col -->
	</div><!-- /.row -->

<?php } elseif ($panel == 'SalesPanel') { ?>
	<div class="row">
		<div class="col-md-12 col-xs-12">
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<div class="col-md-12 header">
					<h3> Manage Sales </h3>
				</div>

				<?php if (array_search("sales/product", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>sales/product">
								<div class="logo">
									<i class="menu-icon ri-luggage-cart-line"></i>
								</div>
								<div class="textModule">
									Sales Add
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("salesrecord", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>salesrecord">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule">
									Sales Record
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("exchange", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>exchange">
								<div class="logo">
									<i class="menu-icon ri-exchange-line"></i>
								</div>
								<div class="textModule">
									Sales Exchange
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<!-- <?php if (array_search("sales/service", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>sales/service">
								<div class="logo">
									<i class="menu-icon fa fa-shopping-bag"></i>
								</div>
								<div class="textModule">
									Service Add
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?> -->

				<?php if (array_search("exchange_record", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>exchange_record">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule">
									Exchange Record
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("salesinvoice", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>salesinvoice">
								<div class="logo">
									<i class="menu-icon ri-file-list-3-line"></i>
								</div>
								<div class="textModule">
									Sales Invoice
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("salesReturn", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>salesReturn">
								<div class="logo">
									<i class="menu-icon ri-refund-fill"></i>
								</div>
								<div class="textModule">
									Sale Return
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("returnList", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>returnList">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule" style="margin: 0;">
									Sale Return Record
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("special_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>special_report">
								<div class="logo">
									<i class="menu-icon ri-file-list-fill"></i>
								</div>
								<div class="textModule" style="margin: 0;">
									Special Report
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("currentStock", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>currentStock">
								<div class="logo">
									<i class="menu-icon ri-store-line"></i>
								</div>
								<div class="textModule" style="margin: 0;">
									Stock Report
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<!-- <?php if (array_search("quotation", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>quotation">
								<div class="logo">
									<i class="menu-icon fa fa-shopping-bag"></i>
								</div>
								<div class="textModule">
									Quotation Entry
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("quotation_record", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>quotation_record">
								<div class="logo">
									<i class="menu-icon fa fa-list"></i>
								</div>
								<div class="textModule">
									Quotation Record
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("quotation_invoice_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>quotation_invoice_report">
								<div class="logo">
									<i class="menu-icon fa fa-file-text-o"></i>
								</div>
								<div class="textModule">
									Quotation Invoice
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?> -->
			</div>
		</div>
	</div>

<?php } elseif ($panel == 'InventoryPanel') { ?>
	<div class="row">
		<div class="col-md-12 col-xs-12">
			<!-- PAGE CONTENT BEGINS -->
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<!-- Header Logo -->
				<div class="col-md-12 header">
					<h3> Manage Inventory </h3>
				</div>
				<?php if (array_search("purchase_order", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>purchase_order">
								<div class="logo">
									<i class="menu-icon ri-shopping-cart-line"></i>
								</div>
								<div class="textModule">
									Purchase Order
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("purchaseorderRecord", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>purchaseorderRecord">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule">
									Order Record
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("purchase", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>purchase">
								<div class="logo">
									<i class="menu-icon ri-shopping-cart-fill"></i>
								</div>
								<div class="textModule">
									Purchase Add
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("purchaseRecord", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>purchaseRecord">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule">
									Purchase Record
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("purchaseInvoice", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>purchaseInvoice">
								<div class="logo">
									<i class="menu-icon  ri-file-list-3-line"></i>
								</div>
								<div class="textModule">
									Purchase Invoice
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("purchaseReturns", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>purchaseReturns">
								<div class="logo">
									<i class="menu-icon ri-refund-fill"></i>
								</div>
								<div class="textModule">
									Purchase Return
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("returnsList", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>returnsList">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule" style="margin-top: 0; line-height: 14px;">
									Purchase Return Record
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("AssetsEntry", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>AssetsEntry">
								<div class="logo">
									<i class="menu-icon ri-funds-line"></i>
								</div>
								<div class="textModule">
									Asset Entry
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("supplierDue", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>supplierDue">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule" style="margin-top: 0; line-height: 14px;">
									Supplier Due
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("supplierPaymentReport", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>supplierPaymentReport">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule" style="margin-top: 0; line-height: 14px;">
									SupplierPayment Report
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("supplierList", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>supplierList" target="_blank">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule">
									Supplier List
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("reorder_list", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>reorder_list">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule">
									ReOrder List
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("currentStock", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>currentStock">
								<div class="logo">
									<i class="menu-icon ri-store-line"></i>
								</div>
								<div class="textModule" style="margin: 0;">
									Stock Report
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<!-- PAGE CONTENT ENDS -->
		</div><!-- /.col -->
	</div><!-- /.row -->

<?php } elseif ($panel == 'AccountsPanel') { ?>
	<div class="row">
		<div class="col-md-12 col-xs-12">
			<!-- PAGE CONTENT BEGINS -->
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<!-- Header Logo -->
				<div class="col-md-12 header">
					<h3> Manage Account </h3>
				</div>
				<?php if (array_search("cashTransaction", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>cashTransaction">
								<div class="logo">
									<i class="menu-icon ri-briefcase-4-line"></i>
								</div>
								<div class="textModule">
									Cash Transaction
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("bank_transactions", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>bank_transactions">
								<div class="logo">
									<i class="menu-icon ri-bank-line"></i>
								</div>
								<div class="textModule">
									Bank Transactions
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("customerPaymentPage", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>customerPaymentPage">
								<div class="logo">
									<i class="menu-icon ri-hand-coin-line"></i>
								</div>
								<div class="textModule">
									Payment Received
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("supplierPayment", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>supplierPayment">
								<div class="logo">
									<i class="menu-icon ri-secure-payment-line"></i>
								</div>
								<div class="textModule">
									Supplier Payment
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("cash_view", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>cash_view">
								<div class="logo">
									<i class="menu-icon ri-slideshow-view"></i>
								</div>
								<div class="textModule">
									Cash View
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("account", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>account">
								<div class="logo">
									<i class="ri-add-box-line"></i>
								</div>
								<div class="textModule">
									Transaction Accounts
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("bank_accounts", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>bank_accounts">
								<div class="logo">
									<i class="ri-home-office-line"></i>
								</div>
								<div class="textModule">
									Bank Accounts
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("TransactionReport", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>TransactionReport" target="_blank">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule" style="line-height: 13px; margin-top: 0;">
									Cash Transaction Report
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("bank_transaction_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>bank_transaction_report">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule" style="line-height: 13px; margin-top: 0;">
									Bank Transaction Report
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("cash_ledger", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>cash_ledger">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule">
									Cash Ledger
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("bank_ledger", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>bank_ledger">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule">
									Bank Ledger
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<!-- PAGE CONTENT ENDS -->
		</div><!-- /.col -->
	</div><!-- /.row -->

<?php } elseif ($panel == 'HRMPanel') { ?>
	<div class="row">
		<div class="col-md-12 col-xs-12">
			<!-- PAGE CONTENT BEGINS -->
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<!-- Header Logo -->
				<div class="col-md-12 header">
					<h3> Manage HRM </h3>
				</div>
				<?php if (array_search("salary_payment", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>salary_payment">
								<div class="logo">
									<i class="menu-icon ri-cash-line"></i>
								</div>
								<div class="textModule">
									Salary Payment
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("salary_payment_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>salary_payment_report">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule" style="line-height: 13px;">
									Payment Report
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("employee", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>employee">
								<div class="logo">
									<i class="menu-icon ri-user-add-line"></i>
								</div>
								<div class="textModule">
									Employee Entry
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("emplists/all", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>emplists/all">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule">
									Employee List
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("designation", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>designation">
								<div class="logo">
									<i class="menu-icon ri-add-circle-line"></i>
								</div>
								<div class="textModule">
									Designation Entry
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("depertment", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>depertment">
								<div class="logo">
									<i class="menu-icon ri-add-circle-line"></i>
								</div>
								<div class="textModule">
									Department Entry
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("month", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>month">
								<div class="logo">
									<i class="menu-icon ri-add-circle-line"></i>
								</div>
								<div class="textModule">
									Month Entry
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<!-- PAGE CONTENT ENDS -->
		</div><!-- /.col -->
	</div><!-- /.row -->

<?php } elseif ($panel == 'ReportsPanel') { ?>

	<div class="row">
		<div class="col-md-12 col-xs-12">
			<!-- PAGE CONTENT BEGINS -->
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<!-- Header Logo -->
				<div class="col-md-12 header">
					<h3> Reports Manage </h3>
				</div>

				<?php if (array_search("profitLoss", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>profitLoss">
								<div class="logo">
									<i class="menu-icon ri-bar-chart-box-ai-line"></i>
								</div>
								<div class="textModule" style="line-height: 13px;">
									Profit & Loss Report
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("cash_view", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>cash_view">
								<div class="logo">
									<i class="menu-icon ri-slideshow-view"></i>
								</div>
								<div class="textModule">
									Cash View
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if ($this->session->userdata('BRANCHid') == 1 && (array_search("supplierDue", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin))) : ?>
					<div class="col-md-2 col-xs-6 custom-padding">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>supplierDue">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule" style="line-height: 13px; margin-top: 0;">
									Supplier Due
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if ($this->session->userdata('BRANCHid') == 1 && (array_search("supplierPaymentReport", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin))) : ?>
					<div class="col-md-2 col-xs-6 custom-padding">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>supplierPaymentReport">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule" style="line-height: 13px; margin-top: 0;">
									Supplier Ledger
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if ($this->session->userdata('BRANCHid') == 1 && (array_search("supplierList", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin))) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>supplierList" target="_blank">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule">
									Supplier List
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("customerDue", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>customerDue">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule">
									Customer Due
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("customerPaymentReport", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>customerPaymentReport">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule" style="line-height: 13px; margin-top: 0;">
									Customer Ledger
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("customer_payment_history", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>customer_payment_history">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule" style="line-height: 13px; margin-top: 0;">
									Customer Payment History
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("customerlist", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>customerlist">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule">
									Customer List
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("currentStock", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>currentStock">
								<div class="logo">
									<i class="menu-icon ri-store-line"></i>
								</div>
								<div class="textModule">
									Stock Report
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("TransactionReport", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>TransactionReport">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule" style="line-height: 13px; margin-top: 0;">
									CashTransaction Report
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("bank_transaction_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>bank_transaction_report">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule" style="line-height: 13px; margin-top: 0;">
									Bank Transaction Report
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
				<?php if (array_search("bank_ledger", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding ">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>bank_ledger">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule" style="line-height: 13px; margin-top: 0;">
									Bank Ledger
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<!-- <?php if (array_search("cashStatment", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>cashStatment">
								<div class="logo">
									<i class="menu-icon fa fa-money"></i>
								</div>
								<div class="textModule">
									Cash Statement
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?> -->

				<!-- <?php if (array_search("BalanceSheet", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>BalanceSheet">
								<div class="logo">
									<i class="menu-icon fa fa-money"></i>
								</div>
								<div class="textModule">
									Balance In Out
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?> -->

				<?php if (array_search("emplists/all", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>emplists/all">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule">
									Employee List
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>

				<?php if (array_search("salary_payment_report", $access) > -1 || isset($CheckSuperAdmin) || isset($CheckAdmin)) : ?>
					<div class="col-md-2 col-xs-6 custom-padding">
						<div class="col-md-12 section20">
							<a href="<?php echo base_url(); ?>salary_payment_report">
								<div class="logo">
									<i class="menu-icon ri-list-radio"></i>
								</div>
								<div class="textModule" style="line-height: 13px; margin-top: 0;">
									Payment Report
								</div>
							</a>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<!-- PAGE CONTENT ENDS -->
		</div><!-- /.col -->
	</div><!-- /.row -->
<?php } ?>