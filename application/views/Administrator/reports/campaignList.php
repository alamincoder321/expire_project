<style>
    .v-select {
        margin-bottom: 5px;
        float: right;
        min-width: 200px;
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

    #priceList label {
        font-size: 13px;
        margin-top: 3px;
    }

    #priceList select {
        border-radius: 3px;
        padding: 0px;
        font-size: 13px;
    }

    #priceList .form-group {
        margin-right: 10px;
    }

    tr td,
    tr th {
        vertical-align: middle !important;
    }
</style>
<div id="productList">
    <div class="row" style="border-bottom: 1px solid #ccc;padding: 5px 0;">
        <div class="col-md-12">
            <form class="form-inline" @submit.prevent="getProducts">
                <div class="form-group">
                    <label>Search Type</label>
                    <select class="form-control" style="padding: 0;" v-model="searchType" @change="onChangeType">
                        <option value="">All</option>
                        <option value="category">By Category</option>
                    </select>
                </div>

                <div class="form-group" style="display:none;" v-bind:style="{display: searchType == 'category' ? '' : 'none'}">
                    <label>Category</label>
                    <v-select v-bind:options="categories" v-model="selectedCategory" label="ProductCategory_Name"></v-select>
                </div>

                <div class="form-group" style="margin-top: -5px;">
                    <input type="submit" value="Search">
                </div>
            </form>
        </div>
    </div>
    <div style="display:none;" v-bind:style="{display: products.length > 0 ? '' : 'none'}">
        <div class="row">
            <div class="col-md-12">
                <a href="" style="margin: 7px 0;display:block;width:50px;" v-on:click.prevent="print">
                    <i class="fa fa-print"></i> Print
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive" id="reportTable">
                    <table class="table table-bordered table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Date From</th>
                                <th>Date To</th>
                                <th>Sale Price</th>
                                <th>Range Quantity</th>
                                <th>Campaign Product</th>
                                <th>Offer Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(product, sl) in products">
                                <tr>
                                    <td :rowspan="product.campaignProducts.length == 0 ? 1 : product.campaignProducts.length" style="text-align:center;">{{ sl + 1 }}</td>
                                    <td :rowspan="product.campaignProducts.length == 0 ? 1 : product.campaignProducts.length">{{ product.Product_Code }}</td>
                                    <td :rowspan="product.campaignProducts.length == 0 ? 1 : product.campaignProducts.length">{{ product.Product_Name }}</td>
                                    <td :rowspan="product.campaignProducts.length == 0 ? 1 : product.campaignProducts.length">{{ product.ProductCategory_Name }}</td>
                                    <td :rowspan="product.campaignProducts.length == 0 ? 1 : product.campaignProducts.length">{{ product.dateFrom | dateFormat('DD-MM-YYYY') }}</td>
                                    <td :rowspan="product.campaignProducts.length == 0 ? 1 : product.campaignProducts.length">{{ product.dateTo | dateFormat('DD-MM-YYYY') }}</td>
                                    <td :rowspan="product.campaignProducts.length == 0 ? 1 : product.campaignProducts.length">{{ product.Product_SellingPrice }}</td>
                                    <td :rowspan="product.campaignProducts.length == 0 ? 1 : product.campaignProducts.length">{{ product.range_quantity }}</td>
                                    <td style="background: none;">{{ product.campaignProducts[0].Product_Name }} - {{product.campaignProducts[0].Product_Code}}</td>
                                    <td style="background: none;text-align:center;">{{ product.campaignProducts[0].offer_quantity }}</td>
                                </tr>
                                <tr v-for="(offerProduct, ind) in product.campaignProducts.slice(1)" v-if="productType == 'offer' && product.productType == 'offer' && product.campaignProducts.length > 1">
                                    <td>{{ offerProduct.Product_Name }} - {{offerProduct.Product_Code}}</td>
                                    <td style="text-align: center;">{{ offerProduct.offer_quantity }}</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>


<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#productList',
        data() {
            return {
                searchType: '',
                productType: "offer",
                categories: [],
                selectedCategory: null,
                products: [],
            }
        },
        filters: {
            dateFormat(dt, format) {
                return dt == null || dt == '' ? '' : moment(dt).format(format);
            }
        },
        created() {
            this.getCategories();
            if (this.productType == 'offer') {
                this.getProducts();
            }
        },
        methods: {
            getCategories() {
                axios.get('/get_categories').then(res => {
                    this.categories = res.data;
                })
            },
            onChangeType(){
                this.selectedCategory = null;
                this.products = [];
            },
            getProducts() {
                let data = {
                    productType: this.productType,
                    categoryId: this.selectedCategory ? this.selectedCategory.ProductCategory_SlNo : ''
                }
                axios.post('/get_products', data).then(res => {
                    this.products = res.data;
                })
            },
            async print() {
                let reportContent = `
					<div class="container">
                        <div class="row">
                            <div class="col-xs-12">
                                <h4 style="text-align:center">Campaign Product List</h4>
                            </div>
                        </div>
					</div>
					<div class="container">
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportTable').innerHTML}
							</div>
						</div>
					</div>
				`;

                var mywindow = window.open('', 'PRINT', `width=${screen.width}, height=${screen.height}`);
                mywindow.document.write(`
                    <style>
                            tr td,
                            tr th {
                                vertical-align: middle !important;
                            }
                    </style>
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