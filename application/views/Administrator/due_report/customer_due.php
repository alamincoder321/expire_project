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

	@media print {
		.no-print {
			display: none !important;
		}
	}
</style>

<div class="row" id="customerDueList">
	<div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;">
		<form class="form-inline">
			<div class="form-group">
				<label>Search Type</label>
				<select class="form-control"  style="width:150px;" v-model="searchType" v-on:change="onChangeSearchType" style="padding:0px;">
					<option value="all">All</option>
					<option value="customer">By Customer</option>
					<option value="employee">By Employee</option>
					<option value="area">By Area</option>
				</select>
			</div>
			<div class="form-group" style="display: none" v-bind:style="{display: searchType == 'customer' ? '' : 'none'}">
				<label>Select Customer</label>
				<v-select v-bind:options="customers" v-model="selectedCustomer" label="display_name" placeholder="Select customer"></v-select>
			</div>
			<div class="form-group" style="display: none" v-bind:style="{display: searchType == 'employee' ? '' : 'none'}">
				<label>Select Employee</label>
				<v-select v-bind:options="employees" v-model="selectedEmployee" label="display_name" placeholder="Select employee"></v-select>
			</div>
			<div class="form-group" style="display: none" v-bind:style="{display: searchType == 'area' ? '' : 'none'}">
				<label>Select Area</label>
				<v-select v-bind:options="areas" v-model="selectedArea" label="District_Name" placeholder="Select area"></v-select>
			</div>

			<div class="form-group">
				<input type="button" class="btn btn-primary" value="Show Report" v-on:click="getDues" style="margin-top: -4px; border: 0px; padding: 3px 6px;">
			</div>
		</form>
	</div>

	<div class="col-md-12" style="display: none" v-bind:style="{display: dues.length > 0 ? '' : 'none'}">
		<a href="" style="margin: 7px 0;display:inline-block;width:50px;" v-on:click.prevent="print">
			<i class="fa fa-print"></i> Print
		</a>
		<button type="button" class="btn btn-success btn-sm" style="margin-bottom:7px;" v-on:click.prevent="excelExport">
			<i class="fa fa-file-excel-o"></i> Export Excel
		</button>

		<div class="row no-print" style="margin: 0 0 15px;" v-bind:style="{display: selectedCustomers.length > 0 ? '' : 'none'}">
			<div class="col-md-12">
				<div class="form-group">
					<label>Due Message ({{ selectedCustomers.length }} customer(s) selected)</label>
					<textarea class="form-control" v-model="dueMessageText" style="height:80px;" placeholder="Type due reminder message..."></textarea>
					<p class="help-block" style="margin-bottom:5px;">Use <code>{name}</code> and <code>{due}</code> in the message, they will be replaced with each customer's name and due amount. <a href="" v-on:click.prevent="resetDueMessage">Reset to default</a></p>
				</div>
				<button type="button" class="btn btn-primary btn-sm" v-on:click="sendDueMessage" v-bind:disabled="sendingMessage">
					<i class="fa fa-send"></i> {{ sendingMessage ? 'Sending...' : 'Send Due Message' }}
				</button>
			</div>
		</div>

		<div class="table-responsive" id="reportTable">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="no-print"><input type="checkbox" v-on:click="selectAllDue" title="Select customers with due amount 5 taka or more"></th>
						<th>Customer Id</th>
						<th>Customer Name</th>
						<th>Owner Name</th>
						<th>Address</th>
						<th>Customer Mobile</th>
						<th>Due Amount</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="data in dues">
						<td class="no-print">
							<input type="checkbox" v-bind:value="data.Customer_Mobile" v-model="selectedCustomers"
								v-bind:disabled="parseFloat(data.dueAmount) < minDueForSelect"
								v-bind:title="parseFloat(data.dueAmount) < minDueForSelect ? 'Minimum ' + minDueForSelect + ' taka due needed to select' : ''">
						</td>
						<td>{{ data.Customer_Code }}</td>
						<td>{{ data.Customer_Name }}</td>
						<td>{{ data.owner_name }}</td>
						<td>{{ data.Customer_Address }}</td>
						<td>{{ data.Customer_Mobile }}</td>
						<td style="text-align:right">{{ parseFloat(data.dueAmount).toFixed(2) }}</td>
					</tr>
				</tbody>
				<tfoot>
					<tr style="font-weight:bold;">
						<td class="no-print"></td>
						<td colspan="5" style="text-align:right">Total Due</td>
						<td style="text-align:right">{{ parseFloat(totalDue).toFixed(2) }}</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#customerDueList',
		data() {
			return {
				searchType: 'all',
				customers: [],
				selectedCustomer: null,
				employees: [],
				selectedEmployee: null,
				areas: [],
				selectedArea: null,
				dues: [],
				totalDue: 0.00,
				selectedCustomers: [],
				dueMessageTemplate: 'Dear Sir/Madam, \nGreetings from Bandhon Departmental Store. Your current outstanding due is BDT {due}. For your acknowledgement. \n\nBandhon Team',
				dueMessageText: '',
				minDueForSelect: 5,
				sendingMessage: false
			}
		},
		created() {
			this.dueMessageText = this.dueMessageTemplate;
		},
		methods: {
			onChangeSearchType() {
				this.selectedCustomer = null;
				this.selectedEmployee = null;
				this.selectedArea = null;
				if (this.searchType == 'customer') {
					this.getCustomers();
				} else if (this.searchType == 'area') {
					this.getAreas();
				} else if (this.searchType == 'employee') {
					this.getEmployees();
				}
			},
			getEmployees() {
				axios.get('/get_employees').then(res => {
					this.employees = res.data.map(item => {
						item.display_name = `${item.Employee_Name} - ${item.Employee_ID}`;
						return item;
					});
				})
			},
			getCustomers() {
				axios.get('/get_customers').then(res => {
					this.customers = res.data;
				})
			},
			getAreas() {
				axios.get('/get_districts').then(res => {
					this.areas = res.data;
				})
			},
			getDues() {
				if (this.searchType == 'customer' && this.selectedCustomer == null) {
					alert('Select customer');
					console.log(this.selectedCustomer);
					return;
				}

				this.selectedCustomers = [];

				let customerId = this.selectedCustomer == null ? null : this.selectedCustomer.Customer_SlNo;
				let districtId = this.selectedArea == null ? null : this.selectedArea.District_SlNo;
				let employeeId = this.selectedEmployee == null ? null : this.selectedEmployee.Employee_SlNo;
				axios.post('/get_customer_due', {
					customerId: customerId,
					districtId: districtId,
					employeeId: employeeId
				}).then(res => {
					if (this.searchType == 'customer') {
						this.dues = res.data;
					} else {
						this.dues = res.data.filter(d => parseFloat(d.dueAmount) != 0);
					}
					this.totalDue = this.dues.reduce((prev, cur) => {
						return prev + parseFloat(cur.dueAmount)
					}, 0);
				})
			},
			selectAllDue() {
				let checked = event.target.checked;
				if (checked) {
					this.selectedCustomers = [...new Set(
						this.dues
							.filter(d => parseFloat(d.dueAmount) >= this.minDueForSelect)
							.map(d => d.Customer_Mobile)
					)];
				} else {
					this.selectedCustomers = [];
				}
			},
			resetDueMessage() {
				this.dueMessageText = this.dueMessageTemplate;
			},
			sendDueMessage() {
				if (this.selectedCustomers.length == 0) {
					alert('Select at least one customer');
					return;
				}

				if (this.dueMessageText.trim().length == 0) {
					alert('Enter due message text');
					return;
				}

				let selectedDues = this.dues.filter(d => this.selectedCustomers.includes(d.Customer_Mobile));

				this.sendingMessage = true;
				let requests = selectedDues.map(data => {
					let smsText = this.dueMessageText
						.replace(/{name}/g, data.Customer_Name)
						.replace(/{due}/g, Number(parseFloat(data.dueAmount).toFixed(2)).toLocaleString('en-US'));

					return axios.post('/send_sms', {
						number: data.Customer_Mobile,
						smsText: smsText
					});
				});

				Promise.all(requests).then(results => {
					this.sendingMessage = false;
					let successCount = results.filter(res => res.data.success).length;
					alert(`Message sent to ${successCount} of ${selectedDues.length} customer(s)`);
					if (successCount > 0) {
						this.selectedCustomers = [];
					}
				}).catch(() => {
					this.sendingMessage = false;
					alert('Failed to send message');
				});
			},
			excelExport() {
				let onlyData = this.dues.map(data => {
					return {
						'Customer Id': data.Customer_Code,
						'Customer Name': data.Customer_Name,
						'Owner Name': data.owner_name,
						'Address': data.Customer_Address,
						'Customer Mobile': data.Customer_Mobile,
						'Due Amount': Number(data.dueAmount)
					}
				})

				const worksheet = XLSX.utils.json_to_sheet(onlyData);
				const workbook = XLSX.utils.book_new();
				XLSX.utils.book_append_sheet(workbook, worksheet, "Customer Due");
				XLSX.writeFile(workbook, "CustomerDueReport.xlsx");
			},
			async print() {
				let reportContent = `
					<div class="container">
						<h4 style="text-align:center">Customer due report</h4 style="text-align:center">
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportTable').innerHTML}
							</div>
						</div>
					</div>
				`;

				var mywindow = window.open('', 'PRINT', `width=${screen.width}, height=${screen.height}`);
				mywindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

				mywindow.document.body.innerHTML += reportContent;

				mywindow.focus();
				await new Promise(resolve => setTimeout(resolve, 1000));
				mywindow.print();
				mywindow.close();
			}
		}
	})
</script>