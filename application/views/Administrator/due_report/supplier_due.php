<style>
	.v-select{
		margin-bottom: 5px;
	}
	.v-select .dropdown-toggle{
		padding: 0px;
	}
	.v-select input[type=search], .v-select input[type=search]:focus{
		margin: 0px;
	}
	.v-select .vs__selected-options{
		overflow: hidden;
		flex-wrap:nowrap;
	}
	.v-select .selected-tag{
		margin: 2px 0px;
		white-space: nowrap;
		position:absolute;
		left: 0px;
	}
	.v-select .vs__actions{
		margin-top:-5px;
	}
	.v-select .dropdown-menu{
		width: auto;
		overflow-y:auto;
	}
</style>

<div id="supplierDue">
	<div class="row">
		<div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;">
			<div class="form-group">
				<label class="col-sm-1 control-label no-padding-right" for="searchType"> Search Type </label>
				<div class="col-sm-2">
					<select id="searchType" class="form-control" style="padding: 0px 3px" v-model="searchType" v-on:change="onChangeSearchType">
						<option value="all"> All </option>
						<option value="supplier"> By Supplier </option>
					</select>
				</div>
			</div>

			<div class="form-group" style="display:none" v-bind:style="{display: searchType == 'supplier' ? '' : 'none'}">
				<label class="col-sm-1 control-label no-padding-right" for="searchType"> Suppliers </label>
				<div class="col-sm-2">
					<v-select v-bind:options="suppliers" v-model="selectedSupplier" label="Supplier_Name"></v-select>
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm-2">
					<input type="button" class="btn btn-primary" value="Show Report" v-on:click="getDues" style="margin-top:0px;border:0px;height:28px;">
				</div>
			</div>
		</div>
	</div>
	<div class="row" style="display:none;" v-bind:style="{display: dueList.length > 0 ? '' : 'none'}">
		<div class="col-md-12">
			<a href="" style="margin: 7px 0;display:inline-block;width:50px;" v-on:click.prevent="print">
				<i class="fa fa-print"></i> Print
			</a>
			<button type="button" class="btn btn-success btn-sm" style="margin-bottom:7px;" v-on:click.prevent="excelExport">
				<i class="fa fa-file-excel-o"></i> Export Excel
			</button>
			<div class="table-responsive" id="reportTable">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Supplier Code</th>
							<th>Supplier Name</th>
							<th>Owner Name</th>
							<th>Address</th>
							<th>Mobile</th>
							<th>Due</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="due in dueList">
							<td>{{ due.Supplier_Code }}</td>
							<td>{{ due.Supplier_Name }}</td>
							<td>{{ due.contact_person }}</td>
							<td>{{ due.Supplier_Address }}</td>
							<td>{{ due.Supplier_Mobile }}</td>
							<td>{{ due.due }}</td>
						</tr>
					</tbody>
					<tbody>
						<tr style="font-weight:bold">
							<td colspan="5" style="text-align:right">Total due</td>
							<td>{{ total.toFixed(2) }}</td>
						</tr>
					</tbody>
				</table>
			</div>
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
		el: '#supplierDue',
		data() {
			return {
				searchType: 'all',
				suppliers: [],
				selectedSupplier: {
					Supplier_SlNo: null,
					Supplier_Name: 'Select Supplier'
				},
				dueList: [],
				total: 0.00
			}
		},
		methods: {
			getSuppliers() {
				axios.get('/get_suppliers').then(res => {
					this.suppliers = res.data;
				})
			},
			onChangeSearchType() {
				if (this.searchType == 'supplier' && this.suppliers.length == 0) {
					this.getSuppliers();
				} else if (this.searchType == 'all') {
					this.selectedSupplier.Supplier_SlNo = null;
				}
			},
			getDues() {
				if (this.searchType == 'supplier' && this.selectedSupplier.Supplier_SlNo == null) {
					alert('Select supplier');
					return;
				}

				axios.post('/get_supplier_due', {
					supplierId: this.selectedSupplier.Supplier_SlNo
				}).then(res => {
					if(this.searchType == 'supplier'){
						this.dueList = res.data;
					} else {
						this.dueList = res.data.filter(d => parseFloat(d.due) != 0);
					}
					this.total = this.dueList.reduce((prev, curr) => {return prev + parseFloat(curr.due)}, 0);
				})
			},
			excelExport() {
				let onlyData = this.dueList.map(due => {
					return {
						'Supplier Code': due.Supplier_Code,
						'Supplier Name': due.Supplier_Name,
						'Owner Name': due.contact_person,
						'Address': due.Supplier_Address,
						'Mobile': due.Supplier_Mobile,
						'Due': Number(due.due)
					}
				})

				const worksheet = XLSX.utils.json_to_sheet(onlyData);
				const workbook = XLSX.utils.book_new();
				XLSX.utils.book_append_sheet(workbook, worksheet, "Supplier Due");
				XLSX.writeFile(workbook, "SupplierDueReport.xlsx");
			},
			async print(){
				let reportContent = `
					<div class="container">
						<h4 style="text-align:center">Supplier due report</h4 style="text-align:center">
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportTable').innerHTML}
							</div>
						</div>
					</div>
				`;

				var mywindow = window.open('', 'PRINT', `width=${screen.width}, height=${screen.height}`);
				mywindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php');?>
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