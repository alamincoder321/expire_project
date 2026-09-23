<style>
    #saleRateManagement .rate-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px 15px;
        margin-bottom: 8px;
        background: #fff;
    }

    #saleRateManagement .rate-value {
        font-size: 18px;
        font-weight: 600;
    }

    #saleRateManagement .rate-status {
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 12px;
    }

    #saleRateManagement .active-rate {
        background: #ecfdf5;
        color: #047857;
    }

    #saleRateManagement .inactive-rate {
        background: #fef2f2;
        color: #b91c1c;
    }
</style>

<div id="saleRateManagement" class="container-fluid">
    <div class="row" style="margin-top:15px;">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><i class="fa fa-tags"></i> Sale Rate Management</strong>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label>Product</label>
                        <v-select
                            :options="products"
                            v-model="selectedProduct"
                            label="display_text"
                            @input="loadRates"
                            @search="searchProduct"
                            placeholder="Select product">
                        </v-select>
                    </div>

                    <div v-if="selectedProduct">
                        <div class="alert alert-info" style="margin-bottom:15px;">
                            <strong>{{ selectedProduct.Product_Name }}</strong>
                            <span v-if="selectedProduct.Product_Code"> - {{ selectedProduct.Product_Code }}</span>
                            <br>
                            <small>Only active rates will appear as selectable sale rates in the sales screen.</small>
                        </div>

                        <div v-if="rates.length == 0" class="alert alert-warning">
                            No sale rates found for this product yet.
                        </div>

                        <div v-for="rate in rates" :key="rate.id" class="rate-card">
                            <div class="row" style="display:flex;align-items:center;">
                                <div class="col-xs-6">
                                    <div class="rate-value">{{ parseFloat(rate.sale_rate).toFixed(2) }}</div>
                                </div>
                                <div class="col-xs-3 text-center">
                                    <span class="rate-status" :class="rate.status == 'a' ? 'active-rate' : 'inactive-rate'">
                                        {{ rate.status == 'a' ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="col-xs-3 text-right">
                                    <button
                                        class="btn btn-xs"
                                        :class="rate.status == 'a' ? 'btn-danger' : 'btn-success'"
                                        @click="toggleRate(rate)">
                                        <i class="fa" :class="rate.status == 'a' ? 'fa-ban' : 'fa-check'"></i>
                                        {{ rate.status == 'a' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center text-muted" style="padding:35px 0;">
                        Select a product to manage its sale rates.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script>
    Vue.component('v-select', VueSelect.VueSelect);

    new Vue({
        el: '#saleRateManagement',
        data() {
            return {
                products: [],
                selectedProduct: null,
                rates: []
            }
        },
        created() {
            this.getProducts();
        },
        methods: {
            getProducts() {
                axios.post('/get_products', {
                        isService: 'false',
                        isMRP: 'yes'
                    })
                    .then(res => {
                        this.products = res.data;
                    });
            },
            searchProduct(val, loading) {
                if (val.length < 2) {
                    return;
                }

                loading(true);
                axios.post('/get_products', {
                    isService: 'false',
                    isMRP: 'yes',
                    name: val
                }).then(res => {
                    this.products = res.data;
                    loading(false);
                }).catch(() => loading(false));
            },
            loadRates() {
                this.rates = [];
                if (!this.selectedProduct) {
                    return;
                }

                axios.post('/get_product_sale_rates', {
                    productId: this.selectedProduct.Product_SlNo
                }).then(res => {
                    this.rates = res.data;
                });
            },
            toggleRate(rate) {
                const newStatus = rate.status == 'a' ? 'd' : 'a';

                axios.post('/toggle_product_sale_rate', {
                    id: rate.id,
                    status: newStatus
                }).then(res => {
                    if (!res.data.success) {
                        alert(res.data.message);
                        return;
                    }

                    rate.status = newStatus;
                }).catch(error => {
                    alert(error.response ? error.response.statusText : 'Something went wrong');
                });
            }
        }
    });
</script>