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
</style>
<div id="cashStatement">
	<div class="row" style="border-bottom: 1px solid #ccc;">
		<div class="col-md-12">
			<form action="" class="form-inline" @submit.prevent="getStatements">
				<?php if (!in_array($this->session->userdata('accountType'), ['u', 'e'])): ?>
					<div class="form-group">
						<label for="">User</label>
						<v-select v-model="selectedUser" :options="users" label="FullName"
							placeholder="Select User"></v-select>
					</div>
				<?php endif; ?>

				<div class="form-group">
					<label for="">Date from</label>
					<input type="date" class="form-control" v-model="filter.dateFrom">
				</div>

				<div class="form-group">
					<label for="">to</label>
					<input type="date" class="form-control" v-model="filter.dateTo">
				</div>

				<div class="form-group buttons">
					<input type="submit" value="Search">
				</div>
			</form>
		</div>
	</div>
	<div class="row" style="display: flex;align-items:center;margin-bottom: 5px;">
		<div class="col-md-6" style="padding-top:10px;">
			<a href="/dayclose" target="_blank" class="btn btn-xs btn-primary" style="padding: 5px 15px;"> Day Close Entry </a>
		</div>
		<div class="col-md-6 text-right" style="padding-top:10px;">
			<a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
		</div>
	</div>

	<div id="printContent">
		<table class="table table-bordered table-hover table-striped">
			<thead>
				<tr>
					<td colspan="11">
						<h2>Transaction Statement</h2>
					</td>
				</tr>
				<tr>
					<td>Name</td>
					<td>Total Sale Amount</td>
					<td>Cash Amount</td>
					<td>Bank Amount</td>
					<td>Change Amount (optional)</td>
					<td>Return Amount</td>
					<td>Exchange Amount</td>
					<td>Due Amount</td>
					<td>C.Receive</td>
					<td>C.Payment</td>
					<td>Close Balance</td>
				</tr>
			</thead>
			<tr v-for="statement in special_report_all">
				<td v-text="statement.AddBy"></td>
				<td v-text="statement.totalsales"></td>
				<td v-text="statement.cashamount"></td>
				<td v-text="statement.bankamount"></td>
				<td v-text="statement.changeamount"></td>
				<td v-text="statement.returnamount"></td>
				<td v-text="statement.exchangeamount"></td>
				<td v-text="statement.dueamount"></td>
				<td v-text="statement.receiveamount"></td>
				<td v-text="statement.paymentamount"></td>
				<td>{{ closeBlance(statement)}}</td>

			</tr>
			<tr style="font-weight: bold; background-color: #ccc">
				<td>Total</td>
				<td v-text="parseFloat(totals_report_all.totalsales).toFixed(2)"></td>
				<td v-text="parseFloat(totals_report_all.cashamount).toFixed(2)"></td>
				<td v-text="parseFloat(totals_report_all.bankamount).toFixed(2)"></td>
				<td v-text="parseFloat(totals_report_all.changeamount).toFixed(2)"></td>
				<td v-text="parseFloat(totals_report_all.returnamount).toFixed(2)"></td>
				<td v-text="parseFloat(totals_report_all.exchangeamount).toFixed(2)"></td>
				<td v-text="parseFloat(totals_report_all.dueamount).toFixed(2)"></td>
				<td v-text="parseFloat(totals_report_all.receiveamount).toFixed(2)"></td>
				<td v-text="parseFloat(totals_report_all.paymentamount).toFixed(2)"></td>
				<td>{{ closeBlance(totals_report_all)}}</td>
			</tr>

			<tr v-if="special_report_all.length == 0">
				<td colspan="11"> No Record Found </td>
			</tr>
		</table>

	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lodash.min.js"></script>

<script>
	Vue.component('v-select', VueSelect.VueSelect);
	new Vue({
		el: '#cashStatement',
		data() {
			return {
				filter: {
					dateFrom: moment().format('YYYY-MM-DD'),
					dateTo: moment().format('YYYY-MM-DD'),
					userFullName: "",
				},
				users: [],
				selectedUser: null,

				special_report_all: [],
				totals_report_all: [],
				userType: "<?= $this->session->userdata('accountType'); ?>"
			}
		},

		filter: {
			decimal(value, fixed) {
				return parseFloat(value).toFixed(fixed);
			}
		},


		async created() {
			await this.getUser();
			await this.getStatements();
		},

		methods: {
			getUser() {
				axios.get('/get_users')
					.then(res => {
						this.users = res.data;
						if (this.userType == 'u' || this.userType == 'e') {
							this.selectedUser = this.users.find(u => u.User_SlNo == "<?= $this->session->userdata('userId'); ?>")
						}
						this.getStatements();
					})
			},
			getStatements() {
				this.special_report_all_data();
			},

			closeBlance(statement) {
				let data = parseFloat(statement.totalsales);
				data += parseFloat(statement.exchangeamount);
				data += parseFloat(statement.receiveamount);
				data -= parseFloat(statement.dueamount);
				data -= parseFloat(statement.returnamount);
				data -= parseFloat(statement.paymentamount);
				data = data.toFixed(2);
				return data;
			},


			special_report_all_data() {
				this.filter.userFullName = this.selectedUser ? this.selectedUser.FullName : "";
				let data = {
					fromDate: this.filter.dateFrom,
					toDate: this.filter.dateTo,
					userFullName: this.filter.userFullName
				}
				axios.post('/special_report_all_data', data)
					.then(res => {
						this.special_report_all = res.data;

						this.totals_report_all = res.data.reduce((acc, sale) => {
							acc.totalsales += parseFloat(sale.totalsales);
							acc.cashamount += parseFloat(sale.cashamount);
							acc.duenamount += parseFloat(sale.duenamount);
							acc.changeamount += parseFloat(sale.changeamount);
							acc.returnamount += parseFloat(sale.returnamount);
							acc.exchangeamount += parseFloat(sale.exchangeamount);
							acc.bankamount += parseFloat(sale.bankamount);
							acc.receiveamount += parseFloat(sale.receiveamount);
							acc.paymentamount += parseFloat(sale.paymentamount);
							return acc;
						}, {
							totalsales: 0,
							cashamount: 0,
							dueamount: 0,
							changeamount: 0,
							returnamount: 0,
							exchangeamount: 0,
							bankamount: 0,
							receiveamount: 0,
							paymentamount: 0
						});
					})
			},


			async print() {
				let printContent = `
					<div class="container">
						<h4 style="text-align:center">Special Report</h4 style="text-align:center">
						<div class="row">
							<div class="col-xs-12 text-center">
								<strong>Statement from</strong> ${this.filter.dateFrom} <strong>to</strong> ${this.filter.dateTo}
							</div>
						</div>
					</div>
					<div class="container">
						${document.querySelector('#printContent').innerHTML}
					</div>
				`;

				var printWindow = window.open('', 'PRINT', `width=${screen.width}, height=${screen.height}`);
				printWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

				printWindow.document.body.innerHTML += printContent;
				printWindow.document.head.innerHTML += `
					<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
					<style>
						th{
							text-align: center;
						}
						.account-section{
							display: flex;
							border: none;
							border-radius: 5px;
							overflow:hidden;
							margin-bottom: 20px;
						}

						.account-section h3{
							margin: 10px 0;
							padding: 0;
						}

						.account-section h4{
							margin: 0;
							margin-top: 3px;
						}

						.account-section .col1{
							background-color: #82a253;
							color: white;
							flex: 1;
							text-align: center;
							padding: 10px;
						}
						.account-section .col2{
							background-color: #edf3e2;
							flex: 2;
							padding: 10px;
							align-items: center; 
							text-align:center;
						}
					</style>
				`;

				printWindow.focus();
				await new Promise(resolve => setTimeout(resolve, 1000));
				printWindow.print();
				printWindow.close();
			}
		}
	})
</script>