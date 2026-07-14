<style>
    .v-select {
        margin-bottom: 5px;
    }

    .v-select .dropdown-toggle {
        padding: 0px;
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

    tr td,
    tr th {
        vertical-align: middle !important;
    }
</style>
<div id="campaignProduct">
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="row">
                <div class="col-xs-12 col-md-4">
                    <div class="row" style="border-radius: 5px; border: 2px solid #007ebb; margin: 0px 0px 10px; padding: 7px 0px;">
                        <h3 style="margin: 0; padding-left: 12px;margin-bottom: 10px;border-bottom: 1px solid gray;padding-bottom: 5px;">Campaign Information</h3>
                        <div class="form-group">
                            <label for="" class="col-xs-4 col-md-4">Product:</label>
                            <div class="col-xs-8 col-md-8">
                                <v-select :options="products" v-model="selectedProduct" label="display_text" @input="onChangeProduct" @search="onSearchProduct"></v-select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-xs-4 col-md-4">CampaignTitle:</label>
                            <div class="col-xs-8 col-md-8">
                                <input type="text" class="form-control" v-model="campaign.name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-xs-4 col-md-4">DateFrom:</label>
                            <div class="col-xs-8 col-md-8">
                                <input type="date" class="form-control" v-model="campaign.dateFrom">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-xs-4 col-md-4">DateTo:</label>
                            <div class="col-xs-8 col-md-8">
                                <input type="date" class="form-control" v-model="campaign.dateTo">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-xs-4 col-md-4">Range Qty:</label>
                            <div class="col-xs-8 col-md-8">
                                <input type="number" step="any" min="0" id="range_quantity" class="form-control" v-model="campaign.range_quantity">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-4">
                    <div class="row" style="border-radius: 5px; border: 2px solid #007ebb; margin: 0px 0px 10px; padding: 7px 0px;">
                        <h3 style="margin: 0; padding-left: 12px;margin-bottom: 10px;border-bottom: 1px solid gray;padding-bottom: 5px;">Product Information</h3>
                        <form @submit.prevent="addToCart">
                            <div class="form-group">
                                <label for="" class="col-xs-4 col-md-4">Product:</label>
                                <div class="col-xs-8 col-md-8">
                                    <v-select :options="products" v-model="selectedCampaignProduct" label="display_text" @input="onChangeCampaignProduct" @search="onSearchProduct"></v-select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-xs-4 col-md-4">Quantity:</label>
                                <div class="col-xs-8 col-md-8">
                                    <input type="number" step="any" id="quantity" min="0" class="form-control" autocomplete="off" v-model="selectedCampaignProduct.quantity">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12 col-md-12 text-right">
                                    <button type="submit" class="btn btn-xs btn-danger" style="padding: 3px 15px;border-radius: 5px;">Add to Cart</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-xs-12 col-md-4">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Sl.</th>
                                <th>Campaign Product</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(product, index) in cart" :key="index" style="display: none;" :style="{ display: cart.length > 0 ? '' : 'none' }">
                                <td>{{ index + 1 }}</td>
                                <td>{{ product.Product_Name }} - {{product.Product_Code}}</td>
                                <td>{{ product.quantity }}</td>
                                <td>
                                    <button class="text-danger" @click="cart.splice(index, 1); calculateTotal();"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr v-if="cart.length == 0">
                                <td colspan="4" class="text-center">No data found</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" style="text-align:right;"><button class="btn btn-xs btn-success" type="button" @click="addCampainProduct"><span v-html="campaign.id != '' ? 'Update' : 'Save'"></span> Campaign</button></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <hr style="margin-top: 10px;margin-bottom: 10px;">
    <div class="row">
        <div class="col-sm-12 form-inline">
            <div class="form-group">
                <label for="filter" class="sr-only">Filter</label>
                <input type="text" class="form-control" v-model="filter" placeholder="Filter">
            </div>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <datatable :columns="columns" :data="campaigns" :filter-by="filter">
                    <template scope="{ row }">
                        <tr>
                            <td>{{ row.sl }}</td>
                            <td>{{ row.name }}</td>
                            <td>{{ row.Product_Name }}</td>
                            <td>{{ row.dateFrom }}</td>
                            <td>{{ row.dateTo }}</td>
                            <td>{{ row.range_quantity }}</td>
                            <td>
                                <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                    <button type="button" class="button edit" @click="editCampaignProduct(row)">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button type="button" class="button" @click="deleteCampaignProduct(row.id)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                <?php } ?>
                            </td>
                        </tr>
                    </template>
                </datatable>
                <datatable-pager v-model="page" type="abbreviated" :per-page="per_page"></datatable-pager>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#campaignProduct',
        data() {
            return {
                campaign: {
                    id: '',
                    product_id: '',
                    name: '',
                    dateFrom: moment().format('YYYY-MM-DD'),
                    dateTo: moment().format('YYYY-MM-DD'),
                    range_quantity: ''
                },
                products: [],
                selectedProduct: null,
                selectedCampaignProduct: {
                    Product_SlNo: '',
                    Product_Name: '',
                    display_text: '',
                    Product_Purchase_Rate: 0,
                    quantity: 0,
                    total: 0
                },
                cart: [],
                userType: '<?php echo $this->session->userdata("accountType"); ?>',
                save_disabled: false,

                campaigns: [],
                columns: [{
                        label: 'Sl',
                        field: 'sl',
                        align: 'center'
                    },
                    {
                        label: 'Name',
                        field: 'name',
                        align: 'center',
                    },
                    {
                        label: 'Campaign Product',
                        field: 'Product_Name',
                        align: 'center'
                    },
                    {
                        label: 'Date From',
                        field: 'dateFrom',
                        align: 'center'
                    },
                    {
                        label: 'Date To',
                        field: 'dateTo',
                        align: 'center'
                    },
                    {
                        label: 'Range Quantity',
                        field: 'range_quantity',
                        align: 'center'
                    },
                    {
                        label: 'Action',
                        align: 'center',
                        filterable: false
                    }
                ],
                page: 1,
                per_page: 10,
                filter: ''
            }
        },
        created() {
            this.getCampaignProducts();
            this.getProducts();
        },
        methods: {
            getCampaignProducts() {
                axios.get('/get_campaign_products')
                    .then(res => {
                        this.campaigns = res.data.map((item, index) => {
                            item.sl = index + 1;
                            return item;
                        });
                    })
            },
            getProducts() {
                axios.post('/get_products', {
                    forSearch: 'yes'
                }).then(res => {
                    this.products = res.data;
                })
            },

            async onSearchProduct(val, loading) {
                if (val.length > 2) {
                    loading(true);
                    await axios.post("/get_products", {
                            name: val
                        })
                        .then(res => {
                            let r = res.data;
                            this.products = r.filter(item => item.status == 'a');
                            loading(false)
                        })
                } else {
                    loading(false)
                    await this.getProducts();
                }
            },


            onChangeProduct() {
                this.campaign.product_id = '';
                if (this.selectedProduct == null) {
                    this.selectedProduct = {
                        Product_SlNo: '',
                        Product_Name: '',
                        display_text: '',
                        Product_Purchase_Rate: 0,
                        quantity: 0,
                        total: 0
                    };
                    return;
                }
                if (this.selectedProduct.Product_SlNo != '') {
                    this.campaign.product_id = this.selectedProduct.Product_SlNo;
                }
            },

            onChangeCampaignProduct() {
                if (this.selectedCampaignProduct == null) {
                    this.selectedCampaignProduct = {
                        Product_SlNo: '',
                        Product_Name: '',
                        display_text: '',
                        Product_Purchase_Rate: 0,
                        quantity: 0,
                        total: 0
                    };
                    return;
                }
                if (this.selectedCampaignProduct.Product_SlNo != '') {
                    document.getElementById('quantity').focus();
                }
            },

            addToCart() {
                if (!this.selectedCampaignProduct.Product_SlNo) {
                    alert('Please select product');
                    return;
                }
                if (!this.selectedCampaignProduct.quantity || this.selectedCampaignProduct.quantity <= 0) {
                    alert('Please enter quantity');
                    return;
                }

                let productInCart = this.cart.find(p => p.Product_SlNo === this.selectedCampaignProduct.Product_SlNo);
                if (productInCart) {
                    productInCart.quantity += parseFloat(this.selectedCampaignProduct.quantity);
                } else {
                    this.cart.push({
                        Product_SlNo: this.selectedCampaignProduct.Product_SlNo,
                        Product_Name: this.selectedCampaignProduct.Product_Name,
                        Product_Code: this.selectedCampaignProduct.Product_Code,
                        quantity: parseFloat(this.selectedCampaignProduct.quantity)
                    });
                }

                // Reset selected campaign product
                this.selectedCampaignProduct = {
                    Product_SlNo: '',
                    Product_Name: '',
                    display_text: '',
                    Product_Purchase_Rate: 0,
                    quantity: 0,
                    total: 0
                };
            },

            addCampainProduct() {
                if (this.cart.length == 0) {
                    alert('Please add campaign products to the cart');
                    return;
                }
                if (this.campaign.product_id == '') {
                    alert('Please select campaign product');
                    return;
                }
                if (this.campaign.name == '') {
                    alert('Please enter campaign title');
                    return;
                }
                if (this.campaign.dateFrom == '') {
                    alert('Please select date from');
                    return;
                }
                if (this.campaign.dateTo == '') {
                    alert('Please select date to');
                    return;
                }
                if (this.campaign.range_quantity == '' || this.campaign.range_quantity <= 0) {
                    alert('Please enter range quantity');
                    return;
                }
                let data = {
                    campaign: this.campaign,
                    cart: this.cart
                }

                let url = '/add_campaign_product';
                if (this.campaign.id != '') {
                    url = '/update_campaign_product';
                }

                axios.post(url, data)
                    .then(res => {
                        if (res.data.success) {
                            alert(res.data.message);
                            this.campaign = {
                                id: '',
                                product_id: '',
                                name: '',
                                dateFrom: moment().format('YYYY-MM-DD'),
                                dateTo: moment().format('YYYY-MM-DD'),
                                range_quantity: ''
                            };
                            this.selectedProduct = null;
                            this.cart = [];
                            this.getCampaignProducts();
                        }
                    })
            },

            editCampaignProduct(campaign) {
                this.campaign.id = campaign.id;
                this.campaign.product_id = campaign.product_id;
                this.campaign.name = campaign.name;
                this.campaign.dateFrom = campaign.dateFrom;
                this.campaign.dateTo = campaign.dateTo;
                this.campaign.range_quantity = campaign.range_quantity;

                this.selectedProduct = this.products.find(p => p.Product_SlNo === campaign.product_id);

                this.cart = campaign.campaignProducts.map(item => ({
                    Product_SlNo: item.product_id,
                    Product_Name: item.Product_Name,
                    Product_Code: item.Product_Code,
                    quantity: parseFloat(item.offer_quantity)
                }));
            },

            deleteCampaignProduct(id) {
                if (confirm('Are you sure to delete this campaign product?')) {
                    axios.post('/delete_campaign_product', {
                            campaignId: id
                        })
                        .then(res => {
                            if (res.data.success) {
                                alert(res.data.message);
                                this.getCampaignProducts();
                            }
                        })
                }
            }
        }
    })
</script>