<style>
	#balanceSheet .buttons {
		margin-top: -5px;
	}

	.balancesheet-table{
		width: 100%;
		border-collapse: collapse;
	}

	.balancesheet-table thead{
		text-align: center;
	}

	.balancesheet-table tfoot{
		font-weight: bold;
		background-color: #eaf3fd;
	}

	.balancesheet-table td, .balancesheet-table th{
		border: 1px solid #ccc;
		padding: 5px;
	}

	.balancesheet-table .row-note{
		font-size: 11px;
		font-weight: normal;
		font-style: italic;
		color: #777;
	}

	.stock-adj-row{
		cursor: pointer;
	}

	.stock-adj-row:hover{
		background-color: #f5f9fd;
	}

	.sv-backdrop{
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: rgba(0, 0, 0, .35);
		z-index: 1040;
	}

	.sv-offcanvas{
		position: fixed;
		top: 0;
		right: 0;
		bottom: 0;
		width: 1000px;
		max-width: 100%;
		background: #fff;
		box-shadow: -4px 0 16px rgba(0, 0, 0, .2);
		z-index: 1050;
		transform: translateX(100%);
		transition: transform .25s ease;
		display: flex;
		flex-direction: column;
	}

	.sv-offcanvas.open{
		transform: translateX(0);
	}

	.sv-header{
		display: flex;
		align-items: center;
		gap: 12px;
		padding: 10px 15px;
		border-bottom: 1px solid #ddd;
		background: #eaf3fd;
	}

	.sv-header h4{
		flex: 1;
		margin: 0;
	}

	.sv-header h4 small{
		margin-left: 6px;
	}

	.sv-close{
		font-size: 18px;
		color: #555;
	}

	.sv-body{
		flex: 1;
		overflow-y: auto;
		padding: 15px;
	}

	.sv-help{
		font-size: 12px;
		color: #555;
	}

	.sv-table{
		width: 100%;
		border-collapse: collapse;
		font-size: 12px;
	}

	.sv-table td, .sv-table th{
		border: 1px solid #ddd;
		padding: 4px 6px;
	}

	.sv-table thead th{
		background: #f3f3f3;
		position: sticky;
		top: 0;
	}

	.sv-table .num{
		text-align: right;
		white-space: nowrap;
	}

	.sv-table .sv-sub td:first-child{
		padding-left: 20px;
		color: #555;
	}

	.sv-table .sv-total td{
		font-weight: bold;
		background: #eaf3fd;
	}

	.sv-toolbar{
		display: flex;
		align-items: center;
		justify-content: space-between;
		gap: 10px;
		margin: 15px 0 6px;
	}

	.sv-toolbar input{
		max-width: 260px;
	}

	.sv-scroll{
		max-height: 60vh;
		overflow: auto;
	}

	.sv-link{
		cursor: pointer;
	}

	.sv-link:hover{
		background: #f5f9fd;
	}

	.sv-muted{
		color: #888;
	}

	.sv-reason{
		font-size: 11px;
		color: #888;
		font-style: italic;
	}

	.sv-pos{
		color: #2e7d32;
	}

	.sv-neg{
		color: #c62828;
	}

	.sv-mismatch td{
		background: #fff6e0;
	}
</style>
<div id="balanceSheet">
	<div class="row" style="border-bottom: 1px solid #ccc;">
		<div class="col-md-12">
			<form action="" class="form-inline" @submit.prevent="getStatements">
				<div class="form-group">
					<input type="date" class="form-control" v-model="date">
				</div>

				<div class="form-group buttons">
					<input type="submit" value="Search">
				</div>
			</form>
		</div>
	</div>

	<div style="display:none;" v-bind:style="{display: showReport ? '' : 'none'}">
		<div class="row" style="margin-top:15px;">
			<div class="col-xs-12">
				<a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
			</div>
		</div>

		<div id="printContent">
			<div class="row" style="margin-top:15px;">
				<div class="col-xs-6">
					<table class="balancesheet-table">
						<thead>
							<tr>
								<td colspan="2">
									<h3>Asset</h3>
								</td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<th>Asset</th>
								<td style="text-align:right;">{{assets | decimal}}</td>
							</tr>
							<tr>
								<th>Cash</th>
								<td style="text-align:right;">{{cash_balance | decimal}}</td>
							</tr>
							<tr>
								<td>	
									<table style="width: 100%;">
										<tr>
											<th colspan="2">Bank:-</th>
										</tr>
										<tr v-for="account in bank_accounts">
											<td>{{account.account_name}}, {{account.bank_name}} ({{account.account_number}})</td>
											<td>{{account.balance | decimal}}</td>
										</tr>
									</table>
								</td>
								<td style="text-align:right;">{{bank_balance | decimal}}</td>
							</tr>
							<tr>
								<th>Customer Due</th>
								<td style="text-align:right;">{{customer_due | decimal}}</td>
							</tr>
							
							<tr>
								<th>Bad Debt</th>
								<td style="text-align:right;">{{bad_debt | decimal}}</td>
							</tr>

							
							<tr>
								<th>Stock Value</th>
								<td style="text-align:right;">{{stock_value | decimal}}</td>
							</tr>
							
							<tr :style="{display: supplier_prev_due != 0 ? '' : 'none'}">
								<th>Supplier Previous Due Adjustment</th>
								<td style="text-align:right;">{{supplier_prev_due | decimal}}</td>
							</tr>
							
							
							<tr :style="{display: loss != 0 ? '' : 'none'}">
								<th>Loss</th>
								<td style="text-align:right;">{{loss | decimal}}</td>
							</tr>

							<tr class="stock-adj-row" :style="{display: stock_adj_loss != 0 ? '' : 'none'}" @click="openStockDetails" title="Click to see full details">
								<th>
									Stock Valuation Adjustment <i class="fa fa-info-circle no-print"></i>
									<div class="row-note">Stock value at current avg. purchase rate is lower than book cost (purchases − cost of sales − returns − damage ± exchange).</div>
								</th>
								<td style="text-align:right;">{{stock_adj_loss | decimal}}</td>
							</tr>

							<tr style="display: none;" v-bind:style="{ display: mis_ad_left > 0 ? '' : 'none' }">
								<th>Miscellaneous Adjustment</th>
								<td style="text-align:right;">{{mis_ad_left | decimal}}</td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th style="text-align:right;">Total Asset</th>
								<td style="text-align:right;">{{totalAsset | decimal}}</td>
							</tr>
						</tfoot>
					</table>
				</div>

				<div class="col-xs-6">
					<table class="balancesheet-table">
						<thead>
							<tr>
								<td colspan="2">
									<h3>Liability</h3>
								</td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>	
									<table style="width: 100%;">
										<tr>
											<th colspan="2">Investment:-</th>
										</tr>
										<tr v-for="account in invest_accounts">
											<td>{{account.Acc_Name}} ({{account.Acc_Code}})</td>
											<td>{{account.balance | decimal}}</td>
										</tr>
									</table>
								</td>
								<td style="text-align:right;">{{invest_balance | decimal}}</td>
							</tr>

							<tr>
								<td>	
									<table style="width: 100%;">
										<tr>
											<th colspan="2">Loan:-</th>
										</tr>
										<tr v-for="account in loan_accounts">
											<td>{{account.account_name}}, {{account.bank_name}} ({{account.account_number}})</td>
											<td>{{account.balance | decimal}}</td>
										</tr>
									</table>
								</td>
								<td style="text-align:right;">{{loan_balance | decimal}}</td>
							</tr>
							<tr>
								<th>Supplier Due</th>
								<td style="text-align:right;">{{supplier_due | decimal}}</td>
							</tr>

							<tr :style="{display: customer_prev_due != 0 ? '' : 'none'}">
								<th>Customer Previous Due Adjustment</th>
								<td style="text-align:right;">{{customer_prev_due | decimal}}</td>
							</tr>
							
							<tr :style="{display: net_profit != 0 ? '' : 'none'}">
								<th>Profit</th>
								<td style="text-align:right;">{{net_profit | decimal}}</td>
							</tr>

							<tr class="stock-adj-row" :style="{display: stock_adj_gain != 0 ? '' : 'none'}" @click="openStockDetails" title="Click to see full details">
								<th>
									Stock Valuation Adjustment <i class="fa fa-info-circle no-print"></i>
									<div class="row-note">Stock value at current avg. purchase rate is higher than book cost (purchases − cost of sales − returns − damage ± exchange).</div>
								</th>
								<td style="text-align:right;">{{stock_adj_gain | decimal}}</td>
							</tr>

							<tr style="display: none;" v-bind:style="{ display: mis_ad_right > 0 ? '' : 'none' }">
								<th>Miscellaneous Adjustment</th>
								<td style="text-align:right;">{{mis_ad_right | decimal}}</td>
							</tr>
							
						</tbody>
						<tfoot>
							<tr>
								<th style="text-align:right;">Total Liability</th>
								<td style="text-align:right;">{{totalLiability | decimal}}</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="sv-backdrop" style="display:none;" v-bind:style="{display: stockPanel.open ? '' : 'none'}" @click="closeStockDetails"></div>
	<div class="sv-offcanvas" :class="{ open: stockPanel.open }">
		<div class="sv-header">
			<a href="" class="sv-back" v-if="stockPanel.ledger" @click.prevent="stockPanel.ledger = null"><i class="fa fa-arrow-left"></i> Back</a>
			<h4>
				<span v-if="!stockPanel.ledger">Stock Valuation Adjustment</span>
				<span v-else>{{ stockPanel.ledger.product.product_name }} ({{ stockPanel.ledger.product.product_code }})</span>
				<small>as of {{ date }}</small>
			</h4>
			<a href="" class="sv-close" @click.prevent="closeStockDetails"><i class="fa fa-times"></i></a>
		</div>

		<div class="sv-body">
			<div class="text-center" v-if="stockPanel.loading"><img src="/assets/loader.gif" alt=""></div>

			<!-- summary + product list -->
			<div v-if="!stockPanel.loading && stockPanel.details && !stockPanel.ledger">
				<p class="sv-help">
					Balance sheet stock value is <strong>quantity × current average purchase rate</strong> of each product.
					Profit and supplier due use <strong>book cost</strong>: what was paid on purchases minus the cost recorded on each sale, return, damage and exchange.
					The difference between the two is the stock valuation adjustment.
				</p>

				<table class="sv-table">
					<tr>
						<td>Stock value (qty × current avg. purchase rate)</td>
						<td class="num">{{ stockPanel.details.stock_value | decimal }}</td>
					</tr>
					<tr class="sv-sub"><td>+ Purchases (invoice sub total)</td><td class="num">{{ stockPanel.details.book_summary.purchase_amount | decimal }}</td></tr>
					<tr class="sv-sub"><td>− Cost of sales (purchase rate saved on sale invoice)</td><td class="num">{{ stockPanel.details.book_summary.sale_cost | decimal }}</td></tr>
					<tr class="sv-sub"><td>+ Cost of sale returns</td><td class="num">{{ stockPanel.details.book_summary.sale_return_cost | decimal }}</td></tr>
					<tr class="sv-sub"><td>− Purchase returns</td><td class="num">{{ stockPanel.details.book_summary.purchase_return_amount | decimal }}</td></tr>
					<tr class="sv-sub"><td>− Damage</td><td class="num">{{ stockPanel.details.book_summary.damage_amount | decimal }}</td></tr>
					<tr class="sv-sub"><td>− Exchange out (cost of items given)</td><td class="num">{{ stockPanel.details.book_summary.exchange_out_cost | decimal }}</td></tr>
					<tr class="sv-sub"><td>+ Exchange in (cost of items taken back)</td><td class="num">{{ stockPanel.details.book_summary.exchange_in_cost | decimal }}</td></tr>
					<tr>
						<td>Stock at book cost</td>
						<td class="num">{{ stockPanel.details.book_summary.total | decimal }}</td>
					</tr>
					<tr class="sv-total">
						<td>Stock Valuation Adjustment</td>
						<td class="num">{{ stockPanel.details.adjustment | decimal }}</td>
					</tr>
				</table>

				<table class="sv-table" style="margin-top:10px;">
					<tr v-for="reason in stockReasons">
						<td>{{ reason.reason }} <span class="sv-muted">({{ reason.count }} products)</span></td>
						<td class="num">{{ reason.amount | decimal }}</td>
					</tr>
					<tr v-if="Math.abs(stockPanel.details.header_detail_difference) >= 0.005">
						<td>Invoice header vs product line rounding</td>
						<td class="num">{{ stockPanel.details.header_detail_difference | decimal }}</td>
					</tr>
				</table>

				<div class="sv-toolbar">
					<strong>Products with a difference ({{ filteredStockProducts.length }})</strong>
					<input type="text" class="form-control input-sm" v-model="stockPanel.search" placeholder="Search product name / code">
				</div>

				<div class="sv-scroll">
					<table class="sv-table sv-list">
						<thead>
							<tr>
								<th>Product</th>
								<th class="num">Qty</th>
								<th class="num">Current Rate</th>
								<th class="num">Stock Value</th>
								<th class="num">Book Cost</th>
								<th class="num">Difference</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="product in filteredStockProducts" @click="openStockLedger(product)" class="sv-link">
								<td>
									{{ product.product_name }} <span class="sv-muted">({{ product.product_code }})</span>
									<div class="sv-reason">{{ product.reason }}</div>
								</td>
								<td class="num">{{ product.current_qty | qty }}</td>
								<td class="num">{{ product.current_rate | decimal }}</td>
								<td class="num">{{ product.stock_value | decimal }}</td>
								<td class="num">{{ product.book_value | decimal }}</td>
								<td class="num" :class="product.difference < 0 ? 'sv-neg' : 'sv-pos'">{{ product.difference | decimal }}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<!-- invoice ledger of one product -->
			<div v-if="!stockPanel.loading && stockPanel.ledger">
				<table class="sv-table">
					<tr><td>Closing quantity</td><td class="num">{{ stockPanel.ledger.closing.qty | qty }}</td></tr>
					<tr><td>Stock value (qty × current rate {{ stockPanel.ledger.product.current_rate | decimal }})</td><td class="num">{{ stockPanel.ledger.closing.stock_value | decimal }}</td></tr>
					<tr><td>Book cost (running balance below)</td><td class="num">{{ stockPanel.ledger.closing.book_value | decimal }}</td></tr>
					<tr class="sv-total"><td>Difference</td><td class="num">{{ stockPanel.ledger.closing.difference | decimal }}</td></tr>
					<tr class="sv-sub">
						<td>From sales / exchanges saved at a cost different from the running average (rows marked below)</td>
						<td class="num">{{ stockPanel.ledger.closing.mismatch_effect | decimal }}</td>
					</tr>
					<tr class="sv-sub">
						<td>
							<span v-if="!stockPanel.ledger.closing.counted">Product is inactive, its stock is not counted in stock value</span>
							<span v-else-if="stockPanel.ledger.closing.average_rate === null">No stock left (or negative), but book cost is not zero</span>
							<span v-else>Current rate {{ stockPanel.ledger.product.current_rate | decimal }} vs running average cost {{ stockPanel.ledger.closing.average_rate | decimal }} (rate changed by purchase edit/delete or manually)</span>
						</td>
						<td class="num">{{ stockPanel.ledger.closing.other_effect | decimal }}</td>
					</tr>
				</table>

				<div class="sv-scroll" style="margin-top:10px;">
					<table class="sv-table sv-list">
						<thead>
							<tr>
								<th>Date</th>
								<th>Type</th>
								<th>Invoice</th>
								<th>Party</th>
								<th class="num">In</th>
								<th class="num">Out</th>
								<th class="num">Cost Rate</th>
								<th class="num">Avg. Before</th>
								<th class="num">Cost In</th>
								<th class="num">Cost Out</th>
								<th class="num">Stock</th>
								<th class="num">Book Cost</th>
								<th class="num">Effect</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="entry in stockPanel.ledger.entries" :class="{ 'sv-mismatch': entry.effect !== null && Math.abs(entry.effect) >= 0.01 }">
								<td style="white-space:nowrap;">{{ entry.date }}</td>
								<td>{{ entry.type }}</td>
								<td>{{ entry.invoice }}</td>
								<td>{{ entry.party }}</td>
								<td class="num">{{ entry.qty_in != 0 ? $options.filters.qty(entry.qty_in) : '' }}</td>
								<td class="num">{{ entry.qty_out != 0 ? $options.filters.qty(entry.qty_out) : '' }}</td>
								<td class="num">{{ entry.rate | decimal }}</td>
								<td class="num">{{ entry.average_before === null ? '' : $options.filters.decimal(entry.average_before) }}</td>
								<td class="num">{{ entry.amount_in != 0 ? $options.filters.decimal(entry.amount_in) : '' }}</td>
								<td class="num">{{ entry.amount_out != 0 ? $options.filters.decimal(entry.amount_out) : '' }}</td>
								<td class="num">{{ entry.balance_qty | qty }}</td>
								<td class="num">{{ entry.balance_book | decimal }}</td>
								<td class="num">{{ entry.effect === null || Math.abs(entry.effect) < 0.01 ? '' : $options.filters.decimal(entry.effect) }}</td>
							</tr>
						</tbody>
					</table>
				</div>
				<p class="sv-help" style="margin-top:8px;">
					<strong>Effect</strong> = (cost rate saved on the invoice − running average cost before it) × quantity.
					A positive effect means more cost was taken out of stock than the average, so book cost became lower than stock value.
				</p>
			</div>
		</div>
	</div>

	<div style="display:none;" v-bind:style="{display: showReport == false ? '' : 'none'}">
		<div class="row">
			<div class="col-md-12 text-center">
				<img src="/assets/loader.gif" alt="">
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
	new Vue({
		el: '#balanceSheet',
		data() {
			return {
				date: moment().format('YYYY-MM-DD'),
				assets: 0.00,
				cash_balance: 0.00,
				bank_balance: 0.00,
				bank_accounts: [],
				customer_due: 0.00,
				bad_debt: 0.00,
				supplier_prev_due: 0.00,
				customer_prev_due: 0.00,
				stock_value: 0.00,
				invest_balance: 0.00,
				invest_accounts: [],
				loan_balance: 0.00,
				loan_accounts: [],
				supplier_due: 0.00,
				net_profit: 0.00,
				loss: 0.00,
				stock_adj_gain: 0.00,
				stock_adj_loss: 0.00,
				totalAsset: 0.00,
				totalLiability: 0.00,
				mis_ad_left: 0.00,
				mis_ad_right: 0.00,
				showReport: null,
				stockPanel: {
					open: false,
					loading: false,
					details: null,
					ledger: null,
					search: ''
				}
			}
		},
		filters: {
			decimal(value) {
				return value == null ? 0.00 : parseFloat(value).toFixed(2);
			},
			qty(value) {
				return value == null ? 0 : parseFloat(parseFloat(value).toFixed(3));
			}
		},
		computed: {
			filteredStockProducts() {
				if (!this.stockPanel.details) {
					return [];
				}
				let search = this.stockPanel.search.toLowerCase().trim();
				return this.stockPanel.details.products.filter(product => {
					return search == '' ||
						String(product.product_name).toLowerCase().includes(search) ||
						String(product.product_code).toLowerCase().includes(search);
				});
			},
			stockReasons() {
				if (!this.stockPanel.details) {
					return [];
				}
				let reasons = {};
				this.stockPanel.details.products.forEach(product => {
					if (!reasons[product.reason]) {
						reasons[product.reason] = { reason: product.reason, count: 0, amount: 0 };
					}
					reasons[product.reason].count++;
					reasons[product.reason].amount += parseFloat(product.difference);
				});
				return Object.values(reasons).sort((a, b) => Math.abs(b.amount) - Math.abs(a.amount));
			}
		},
		// computed: {
		// 	totalAsset() {
		// 		return parseFloat(this.assets) +
		// 			parseFloat(this.cash_balance) +
		// 			parseFloat(this.bank_balance) +
		// 			parseFloat(this.customer_due) +
		// 			parseFloat(this.bad_debt) +
		// 			parseFloat(this.supplier_prev_due) +
		// 			parseFloat(this.loss) +
		// 			parseFloat(this.mis_ad_left) +
		// 			parseFloat(this.stock_value);
		// 	},
		// 	totalLiability() {
		// 		return parseFloat(this.invest_balance) +
		// 			parseFloat(this.loan_balance) +
		// 			parseFloat(this.net_profit) +
		// 			parseFloat(this.customer_prev_due) +
		// 			parseFloat(this.mis_ad_right) +
		// 			parseFloat(this.supplier_due);
		// 	}
		// },
		methods: {
			async getStatements() {
				this.showReport = false;
				await axios.post('/get_balance_sheet', {date : this.date})
				.then(res => {
					if(res.data.success){

						if(res.data.statements.net_profit){
							if(res.data.statements.net_profit >= 0){
								this.net_profit = res.data.statements.net_profit;
								this.loss = 0.00;
							}else{
								this.loss = Math.abs(res.data.statements.net_profit);
								this.net_profit = 0.00;
							}
						}else{
							this.net_profit = 0.00;
							this.loss = 0.00;
						}

						this.assets 			= res.data.statements.assets ?? 0;
						this.cash_balance		= res.data.statements.cash_balance ?? 0;
						this.bank_accounts 		= res.data.statements.bank_accounts;					
						this.customer_due 		= res.data.statements.customer_dues ?? 0;
						this.bad_debt 			= res.data.statements.bad_debts ?? 0;
						this.supplier_prev_due 	= res.data.statements.supplier_prev_due ?? 0;
						this.customer_prev_due 	= res.data.statements.customer_prev_due ?? 0;
						this.stock_value 		= res.data.statements.stock_value ?? 0;
						let stock_adj 			= parseFloat(res.data.statements.stock_valuation_adjustment ?? 0);
						this.stock_adj_gain 	= stock_adj > 0 ? stock_adj : 0.00;
						this.stock_adj_loss 	= stock_adj < 0 ? Math.abs(stock_adj) : 0.00;
						this.invest_accounts	= res.data.statements.invest_accounts;					
						this.loan_accounts 		= res.data.statements.loan_accounts;					
						this.supplier_due 		= res.data.statements.supplier_dues ?? 0;
						this.bank_balance 		= this.bank_accounts.reduce((prev, curr)=>{
							return prev + parseFloat(curr.balance);
						},0).toFixed(2);
						
						this.invest_balance 	= this.invest_accounts.reduce((prev, curr)=>{
							return prev + parseFloat(curr.balance);
						},0).toFixed(2);
						
						this.loan_balance 		= this.loan_accounts.reduce((prev, curr)=>{
							return prev + parseFloat(curr.balance);
						},0).toFixed(2);


						let totalAsset   	= 	parseFloat(this.assets) +
												parseFloat(this.cash_balance) +
												parseFloat(this.bank_balance) +
												parseFloat(this.customer_due) +
												parseFloat(this.bad_debt) +
												parseFloat(this.supplier_prev_due) +
												parseFloat(this.loss) +
												parseFloat(this.stock_adj_loss) +
												parseFloat(this.stock_value);

						let totalLiability 	= 	parseFloat(this.invest_balance) +
												parseFloat(this.loan_balance) +
												parseFloat(this.net_profit) +
												parseFloat(this.customer_prev_due) +
												parseFloat(this.stock_adj_gain) +
												parseFloat(this.supplier_due);

						totalAsset 			= Math.round(totalAsset * 100) / 100;
						totalLiability 		= Math.round(totalLiability * 100) / 100;
												
						if (totalAsset > totalLiability) {
							this.mis_ad_right 	= totalAsset - totalLiability;
							this.mis_ad_left 	= 0.00;

						}else if(totalAsset < totalLiability){
							this.mis_ad_left 	= totalLiability - totalAsset;
							this.mis_ad_right 	= 0.00;
						}else{
							this.mis_ad_left 	= 0.00;
							this.mis_ad_right 	= 0.00;
						}

						this.totalAsset 		= totalAsset + this.mis_ad_left;
						this.totalLiability 	= totalLiability + this.mis_ad_right;

						this.showReport = true;
					}else{
						alert(res.data.message);
					}
				});
			},

			async openStockDetails() {
				this.stockPanel.open = true;
				this.stockPanel.ledger = null;
				this.stockPanel.loading = true;
				await axios.post('/get_stock_valuation_details', { date: this.date })
				.then(res => {
					if (res.data.success) {
						this.stockPanel.details = res.data;
					} else {
						alert(res.data.message);
					}
				})
				.finally(() => {
					this.stockPanel.loading = false;
				});
			},

			async openStockLedger(product) {
				this.stockPanel.loading = true;
				await axios.post('/get_stock_valuation_ledger', { date: this.date, productId: product.product_id })
				.then(res => {
					if (res.data.success) {
						this.stockPanel.ledger = res.data;
					} else {
						alert(res.data.message);
					}
				})
				.finally(() => {
					this.stockPanel.loading = false;
				});
			},

			closeStockDetails() {
				this.stockPanel.open = false;
				this.stockPanel.ledger = null;
			},

			async print() {
				let printContent = `
					<div class="container">
						<h4 style="text-align:center">Balance Sheet</h4 style="text-align:center">
						<div class="row">
							<div class="col-xs-12 text-center">
								Statement of <strong>${this.date}</strong> 
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
					<style>
						.balancesheet-table{
							width: 100%;
							border-collapse: collapse;
						}

						.balancesheet-table thead{
							text-align: center;
						}

						.balancesheet-table tfoot{
							font-weight: bold;
							background-color: #eaf3fd;
						}

						.balancesheet-table td, .balancesheet-table th{
							border: 1px solid #ccc;
							padding: 5px;
						}

						.no-print{
							display: none;
						}

						.balancesheet-table .row-note{
							font-size: 11px;
							font-weight: normal;
							font-style: italic;
							color: #777;
						}
					</style>
					
				`;

				printWindow.focus();
				await new Promise(resolve => setTimeout(resolve, 1000));
				printWindow.print();
				await new Promise(resolve => setTimeout(resolve, 1000));
				printWindow.close();
			}
		}
	})
</script>