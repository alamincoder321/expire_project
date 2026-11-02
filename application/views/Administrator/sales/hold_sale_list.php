<style>
	.v-select {
		margin-top: -2.5px;
		float: right;
		min-width: 180px;
		margin-left: 5px;
	}

	.v-select .dropdown-toggle {
		padding: 0px;
		height: 25px;
	}

	.v-select input[type=search],
	.v-select input[type=search]:focus {
		margin: 0px;
	}

	.v-select .vs__selected-options {
		overflow: hidden;
		flex-wrap: nowrap;
	}

	.v-select .selected-tag {
		margin: 2px 0px;
		white-space: nowrap;
		position: absolute;
		left: 0px;
	}

	.v-select .vs__actions {
		margin-top: -5px;
	}

	.v-select .dropdown-menu {
		width: auto;
		overflow-y: auto;
	}

	#searchForm select {
		padding: 0;
		border-radius: 4px;
	}

	#searchForm .form-group {
		margin-right: 5px;
	}

	#searchForm * {
		font-size: 13px;
	}

	.record-table {
		width: 100%;
		min-width: 900px;
		border-collapse: separate;
		border-spacing: 0;
		border: 1px solid #e3e8ef;
		border-radius: 8px;
		overflow: hidden;
		background: #fff;
	}

	.record-table thead {
		background: #f1f6fb;
	}

	.record-table th {
		color: #334155;
		font-size: 12px;
		font-weight: 700;
		letter-spacing: .03em;
		text-transform: uppercase;
		white-space: nowrap;
	}

	.record-table th,
	.record-table td {
		padding: 10px 12px;
		border: 0;
		border-bottom: 1px solid #e9edf2;
		vertical-align: middle;
	}

	.record-table th {
		text-align: center;
	}

	.record-table tbody tr:not(.total-row):hover {
		background: #f8fbff;
	}

	.record-table .money-cell {
		font-variant-numeric: tabular-nums;
		white-space: nowrap;
	}

	.record-table .note-cell {
		min-width: 140px;
		max-width: 260px;
		white-space: normal;
	}

	.record-table .total-row td {
		background: #f1f6fb;
		border-bottom: 0;
		font-weight: 700;
	}

	.hold-sale-toolbar {
		align-items: center;
		background: #fff;
		border: 1px solid #e3e8ef;
		border-radius: 8px;
		margin: 15px 0 12px;
		padding: 12px 8px;
	}

	.hold-sale-count {
		background: #e8f3ff;
		border-radius: 14px;
		color: #1769aa;
		display: inline-block;
		font-size: 13px;
		font-weight: 700;
		margin-left: 5px;
		min-width: 28px;
		padding: 4px 9px;
		text-align: center;
	}

	.hold-sale-actions {
		white-space: nowrap;
	}

	.hold-sale-actions a {
		align-items: center;
		border: 1px solid #e3e8ef;
		border-radius: 5px;
		color: #1769aa;
		display: inline-flex;
		height: 30px;
		justify-content: center;
		margin: 0 2px;
		text-decoration: none;
		width: 32px;
	}

	.hold-sale-actions a:hover {
		background: #e8f3ff;
	}

	@media (max-width: 767px) {
		.hold-sale-toolbar {
			padding: 10px 4px;
		}

		.hold-sale-toolbar .text-right {
			margin-top: 8px;
			text-align: left;
		}
	}

	.hold-sale-filter {
		background: #fff;
		border: 1px solid #e3e8ef;
		border-radius: 8px;
		margin-top: 15px;
		padding: 12px;
	}

	.hold-sale-filter .form-group {
		margin: 4px 8px 4px 0;
	}

	.hold-sale-filter label {
		color: #475569;
		font-weight: 600;
		margin-right: 4px;
	}

	.hold-sale-filter .form-control {
		min-width: 145px;
	}

	.v-select {
		margin-top: -2.5px;
		float: right;
		min-width: 180px;
		margin-left: 5px;
	}

	.v-select .dropdown-toggle {
		padding: 0px;
		height: 25px;
	}

	.v-select input[type=search],
	.v-select input[type=search]:focus {
		margin: 0px;
	}

	.v-select .vs__selected-options {
		overflow: hidden;
		flex-wrap: nowrap;
	}

	.v-select .selected-tag {
		margin: 2px 0px;
		white-space: nowrap;
		position: absolute;
		left: 0px;
	}

	.v-select .vs__actions {
		margin-top: -5px;
	}

	.v-select .dropdown-menu {
		width: auto;
		overflow-y: auto;
	}
</style>
<div id="salesRecord">

	<div class="row hold-sale-filter">
		<div class="col-md-12">
			<form class="form-inline" id="searchForm" @submit.prevent="getSalesRecord">
				<div class="form-group">
					<label>Customer</label>
					<v-select :options="customers" v-model="selectedCustomer" label="display_name" placeholder="All customers"></v-select>
				</div>
				<div class="form-group">
					<label>Employee</label>
					<v-select :options="employees" v-model="selectedEmployee" label="Employee_Name" placeholder="All employees"></v-select>
				</div>
				<div class="form-group">
					<label>From</label>
					<input type="date" class="form-control" v-model="dateFrom">
				</div>
				<div class="form-group">
					<label>To</label>
					<input type="date" class="form-control" v-model="dateTo">
				</div>
				<div class="form-group">
					<button type="submit"><i class="fa fa-search"></i> Search</button>
					<button type="button" @click="clearFilters">Clear</button>
				</div>
			</form>
		</div>
	</div>

	<div class="row hold-sale-toolbar" style="display:none;" v-bind:style="{display: sales.length > 0 ? '' : 'none'}">
		<div class="col-md-6">
			<a href="" v-on:click.prevent="print">
				<i class="fa fa-print"></i> Print
			</a>
			<?php if ($this->session->userdata('accountType') != 'u') { ?>
				<button type="button" class="btn btn-danger btn-xs" style="margin-left:10px;" :disabled="selectedIds.length == 0" @click="bulkDelete"><i class="fa fa-trash"></i> Delete Selected ({{ selectedIds.length }})</button>
			<?php } ?>
		</div>
		<div class="col-md-5 text-right">
			<strong>Total Hold Sale</strong> <span class="hold-sale-count" v-text="sales.length"></span>
		</div>
		<div class="col-md-1 text-right">
			<a href="" v-on:click.prevent="excelExport">
				<i class="fa fa-file-excel-o"></i> Excel
			</a>
		</div>
		<div class="col-md-12">
			<div class="table-responsive" id="reportContent">
				<table
					class="record-table"
					v-if="(searchTypesForRecord.includes(searchType)) && recordType == 'without_details'"
					style="display:none"
					v-bind:style="{display: (searchTypesForRecord.includes(searchType)) && recordType == 'without_details' ? '' : 'none'}">
					<thead>
						<tr>
							<?php if ($this->session->userdata('accountType') != 'u') { ?><th class="no-print"><input type="checkbox" :checked="sales.length > 0 && selectedIds.length == sales.length" @change="toggleAll($event)"></th><?php } ?>
							<th>Invoice No.</th>
							<th>Date</th>
							<th>Customer Name</th>
							<th>Employee Name</th>
							<th>Saved By</th>
							<th>SubTotal</th>
							<th>Total</th>
							<th>Note</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="sale in sales" :style="{background: (sale.Status == 'p' && sale.web_order == '1') ? 'rgb(252 179 179)' : ''}">
							<?php if ($this->session->userdata('accountType') != 'u') { ?><td class="no-print" style="text-align:center;"><input type="checkbox" :value="sale.SaleMaster_SlNo" v-model="selectedIds"></td><?php } ?>
							<td>{{ sale.SaleMaster_InvoiceNo }}</td>
							<td>{{ sale.SaleMaster_SaleDate }}</td>
							<td>{{ sale.Customer_Name }}</td>
							<td>{{ sale.Employee_Name }}</td>
							<td>{{ sale.AddBy }}</td>
							<td class="money-cell" style="text-align:right;">{{ Number(sale.SaleMaster_SubTotalAmount || 0).toFixed(2) }}</td>
							<td class="money-cell" style="text-align:right;">{{ Number(sale.SaleMaster_TotalSaleAmount || 0).toFixed(2) }}</td>
							<td class="note-cell" style="text-align:left;">{{ sale.SaleMaster_Description }}</td>
							<td class="hold-sale-actions" style="text-align:center;">
								<a href="" title="Resume Hold Sale" v-bind:href="`/hold_sale/${encodeURIComponent(sale.SaleMaster_InvoiceNo)}`" target="_blank"><i class="ri-shopping-cart-line"></i></a>
								<?php if ($this->session->userdata('accountType') != 'u') { ?>
									<a v-if="sale.Status != 'c' && sale.Status != 'd'" href="" title="Delete Hold Sale" @click.prevent="deleteSale(sale.SaleMaster_SlNo)"><i class="fa fa-trash"></i></a>
								<?php } ?>
							</td>
						</tr>
						<tr class="total-row">
							<?php if ($this->session->userdata('accountType') != 'u') { ?><td class="no-print"></td><?php } ?>
							<td colspan="5" style="text-align:right;">Total</td>
							<td class="money-cell" style="text-align:right;">{{ sales.reduce((prev, curr)=>{return prev + (Number(curr.SaleMaster_SubTotalAmount) || 0)}, 0).toFixed(2) }}</td>
							<td class="money-cell" style="text-align:right;">{{ sales.reduce((prev, curr)=>{return prev + (Number(curr.SaleMaster_TotalSaleAmount) || 0)}, 0).toFixed(2) }}</td>
							<td></td>
							<td></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="row" style="margin-top:15px;display:none;text-align:center;font-weight: 700;" v-bind:style="{display: sales.length == 0 ? '' : 'none'}">
		<div class="col-xs-12">
			Not Found Data
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lodash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#salesRecord',
		data() {
			return {
				searchType: '',
				recordType: 'without_details',
				dateFrom: '',
				dateTo: '',
				customers: [],
				selectedCustomer: null,
				employees: [],
				selectedEmployee: null,
				products: [],
				selectedProduct: null,
				users: [],
				selectedUser: null,
				categories: [],
				selectedCategory: null,
				sales: [],
				searchTypesForRecord: ['', 'user', 'customer', 'employee'],
				searchTypesForDetails: ['quantity', 'category'],
				status: 'a',
				selectedIds: []
			}
		},

		created() {
			this.getCustomers();
			this.getEmployees();
			<?php if (isset($_GET['type']) && $_GET['type'] == 'online') { ?>
				this.status = 'p';
			<?php } ?>
			this.getSearchResult();
		},
		methods: {
			checkReturnAndEdit(sale) {
				axios.get('/check_sale_return/' + sale.SaleMaster_InvoiceNo).then(res => {
					if (res.data.found) {
						alert('Unable to edit. Sale return found!');
					} else {
						if (sale.is_service == 'true') {
							location.replace('/sales/service/' + sale.SaleMaster_SlNo);
						} else {
							location.replace('/sales/product/' + sale.SaleMaster_SlNo);
						}
					}
				})
			},
			statusData(status) {
				if (status === 'p') {
					return {
						class: 'label-warning',
						text: 'Pending'
					};
				} else if (status === 'a') {
					return {
						class: 'label-success',
						text: 'Confirm'
					};
				} else if (status === 'c') {
					return {
						class: 'label-danger',
						text: 'Cancel'
					};
				} else if (status === 'r') {
					return {
						class: 'label-danger',
						text: 'Return'
					};
				} else if (status === 'd') {
					return {
						class: 'label-danger',
						text: 'Delete'
					};
				}
			},
			onChangeSearchType() {
				this.sales = [];
				if (this.searchType == 'quantity') {
					this.getProducts();
				} else if (this.searchType == 'user') {
					this.getUsers();
				} else if (this.searchType == 'category') {
					this.getCategories();
				} else if (this.searchType == 'customer') {
					this.getCustomers();
				} else if (this.searchType == 'employee') {
					this.getEmployees();
				}
			},
			getProducts() {
				axios.get('/get_products').then(res => {
					this.products = res.data;
				})
			},
			getCustomers() {
				axios.get('/get_customers').then(res => {
					this.customers = res.data;
				})
			},
			getEmployees() {
				axios.get('/get_employees').then(res => {
					this.employees = res.data;
				})
			},
			getUsers() {
				axios.get('/get_users').then(res => {
					this.users = res.data;
				})
			},
			getCategories() {
				axios.get('/get_categories').then(res => {
					this.categories = res.data;
				})
			},
			getSearchResult() {
				this.getSalesRecord();
			},
			getSalesRecord() {
				this.selectedIds = [];
				let filter = {
					customerId: this.selectedCustomer == null ? '' : this.selectedCustomer.Customer_SlNo,
					employeeId: this.selectedEmployee == null ? '' : this.selectedEmployee.Employee_SlNo,
					dateFrom: this.dateFrom,
					dateTo: this.dateTo,
					status: this.status,
					type: '<?php echo isset($_GET['type']) && $_GET['type'] == 'online' ? 'online' : ''; ?>'
				}

				axios.post('/get_hold_sale', filter)
					.then(res => {
						this.sales = res.data.sales;
					})
			},
			clearFilters() {
				this.selectedCustomer = null;
				this.selectedEmployee = null;
				this.dateFrom = '';
				this.dateTo = '';
				this.getSalesRecord();
			},
			deleteSale(saleId) {
				let deleteConf = confirm('Are you sure?');
				if (deleteConf == false) {
					return;
				}
				axios.post('/delete_hold_sale', {
						holdSaleId: saleId
					})
					.then(res => {
						let r = res.data;
						alert(r.message);
						if (r.success) {
							this.getSalesRecord();
						}
					})
			},
			toggleAll(e) {
				this.selectedIds = e.target.checked ? this.sales.map(s => s.SaleMaster_SlNo) : [];
			},
			bulkDelete() {
				if (this.selectedIds.length == 0) {
					alert('Please select at least one hold sale');
					return;
				}
				if (!confirm(`Delete ${this.selectedIds.length} hold sale(s)?`)) {
					return;
				}
				axios.post('/delete_hold_sale_bulk', {
						holdSaleIds: this.selectedIds
					})
					.then(res => {
						let r = res.data;
						alert(r.message);
						if (r.success) {
							this.selectedIds = [];
							this.getSalesRecord();
						}
					})
			},
			async print() {
				let dateText = '';
				if (this.dateFrom != '' || this.dateTo != '') {
					dateText = `Statement ${this.dateFrom != '' ? `from <strong>${this.dateFrom}</strong>` : ''} ${this.dateTo != '' ? `to <strong>${this.dateTo}</strong>` : ''}`;
				}

				let userText = '';
				if (this.selectedUser != null && this.selectedUser.FullName != '' && this.searchType == 'user') {
					userText = `<strong>Sold by: </strong> ${this.selectedUser.FullName}`;
				}

				let customerText = '';
				if (this.selectedCustomer != null && this.selectedCustomer.Customer_SlNo != '') {
					customerText = `<strong>Customer: </strong> ${this.selectedCustomer.Customer_Name}<br>`;
				}

				let employeeText = '';
				if (this.selectedEmployee != null && this.selectedEmployee.Employee_SlNo != '') {
					employeeText = `<strong>Employee: </strong> ${this.selectedEmployee.Employee_Name}<br>`;
				}

				let productText = '';
				if (this.selectedProduct != null && this.selectedProduct.Product_SlNo != '' && this.searchType == 'quantity') {
					productText = `<strong>Product: </strong> ${this.selectedProduct.Product_Name}`;
				}

				let categoryText = '';
				if (this.selectedCategory != null && this.selectedCategory.ProductCategory_SlNo != '' && this.searchType == 'category') {
					categoryText = `<strong>Category: </strong> ${this.selectedCategory.ProductCategory_Name}`;
				}


				let reportContent = `
					<div class="container">
						<div class="row">
							<div class="col-xs-12 text-center">
								<h3>Sales Record</h3>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-6">
								${userText} ${customerText} ${employeeText} ${productText} ${categoryText}
							</div>
							<div class="col-xs-6 text-right">
								${dateText}
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportContent').innerHTML}
							</div>
						</div>
					</div>
				`;

				var reportWindow = window.open('', 'PRINT', `height=${screen.height}, width=${screen.width}`);
				reportWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

				reportWindow.document.head.innerHTML += `
					<style>
						.record-table{
							width: 100%;
							border-collapse: collapse;
						}
						.record-table thead{
							background-color: #0097df;
							color:white;
						}
						.record-table th, .record-table td{
							padding: 3px;
							border: 1px solid #454545;
						}
						.record-table th{
							text-align: center;
						}
					</style>
				`;
				reportWindow.document.body.innerHTML += reportContent;
				reportWindow.document.querySelectorAll('.no-print').forEach(el => el.remove());

				if (this.searchType == '' || this.searchType == 'user') {
					let rows = reportWindow.document.querySelectorAll('.record-table tr');
					rows.forEach(row => {
						row.lastChild.remove();
					})
				}


				reportWindow.focus();
				await new Promise(resolve => setTimeout(resolve, 1000));
				reportWindow.print();
				reportWindow.close();
			},

			excelExport() {
				let onlyData = this.sales.map(item => {
					return {
						'Invoice No.': item.SaleMaster_InvoiceNo,
						'Date': item.SaleMaster_SaleDate,
						'Customer Name': item.Customer_Name,
						'Employee Name': item.Employee_Name,
						'Saved By': item.AddBy,
						'SubTotal': item.SaleMaster_SubTotalAmount,
						'Discount': item.SaleMaster_TotalDiscountAmount,
						'Point': item.pointAmount,
						'Total': item.SaleMaster_TotalSaleAmount,
						'Paid': item.SaleMaster_PaidAmount,
						'Return': item.returnAmount,
						'Note': item.SaleMaster_Description
					}
				})

				const worksheet = XLSX.utils.json_to_sheet(onlyData);
				const workbook = XLSX.utils.book_new();
				XLSX.utils.book_append_sheet(workbook, worksheet, "Skipped Rows");
				// Excel download
				XLSX.writeFile(workbook, "SaleRecord.xlsx");
			}
		}
	})
</script>