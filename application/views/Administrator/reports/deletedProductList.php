<style scoped>
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
<div id="deletedProductList">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;">
            <form class="form-inline">
                <div class="form-group">
                    <label>Search Type</label>
                    <select class="form-control" style="width:150px;" v-model="searchType" v-on:change="onChangeSearchType" style="padding:0px;">
                        <option value="">All</option>
                        <option value="category">By Category</option>
                    </select>
                </div>
                <div class="form-group" style="display: none" v-bind:style="{display: searchType == 'category' ? '' : 'none'}">
                    <label>Select Category</label>
                    <v-select v-bind:options="categories" v-model="selectedCategory" label="ProductCategory_Name" placeholder="Select category"></v-select>
                </div>

                <div class="form-group">
                    <input type="button" class="btn btn-primary" value="Show Report" v-on:click="getProducts" style="margin-top: -4px; border: 0px; padding: 3px 6px;">
                </div>
            </form>
        </div>
    </div>
    <div style="display:none;" v-bind:style="{display: products.length > 0 ? '' : 'none'}">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive" id="reportTable">
                    <table class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Product Id</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th style="text-align: right;">Purchase Price</th>
                                <th style="text-align: right;">Sale Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(product, sl) in products">
                                <td style="text-align:center;">{{ sl + 1 }}</td>
                                <td>{{ product.Product_Code }}</td>
                                <td>{{ product.Product_Name }}</td>
                                <td>{{ product.ProductCategory_Name }}</td>
                                <td style="text-align:right;">{{ product.Product_Purchase_Rate }}</td>
                                <td style="text-align:right;">{{ product.Product_SellingPrice }}</td>
                                <td>
                                    <button type="button" class="btn btn-success btn-xs" v-on:click="restoreProduct(product)" v-bind:disabled="restoringId == product.Product_SlNo">
                                        <i class="fa fa-undo"></i> {{ restoringId == product.Product_SlNo ? 'Restoring...' : 'Restore' }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div style="display:none;" v-bind:style="{display: searched && products.length == 0 ? '' : 'none'}">
        <p class="text-muted">No deleted products found.</p>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#deletedProductList',
        data() {
            return {
                searchType: '',
                categories: [],
                selectedCategory: null,
                products: [],
                searched: false,
                restoringId: null
            }
        },
        methods: {
            onChangeSearchType() {
                this.selectedCategory = null;
                if (this.searchType == 'category') {
                    this.getCategory();
                }
            },
            getCategory() {
                axios.get('/get_categories').then(res => {
                    this.categories = res.data;
                })
            },
            getProducts() {
                let filter = {
                    categoryId: this.selectedCategory == null ? null : this.selectedCategory.ProductCategory_SlNo,
                    status: 'd'
                }
                axios.post('/get_total_stock', filter).then(res => {
                    this.products = res.data.stock;
                    this.searched = true;
                })
            },
            restoreProduct(product) {
                let confirmRestore = confirm(`Restore product "${product.Product_Name}"?`);
                if (!confirmRestore) {
                    return;
                }

                this.restoringId = product.Product_SlNo;
                axios.post('/restore_product', {
                    productId: product.Product_SlNo
                }).then(res => {
                    let r = res.data;
                    alert(r.message);
                    this.restoringId = null;
                    if (r.success) {
                        this.getProducts();
                    }
                }).catch(() => {
                    this.restoringId = null;
                    alert('Failed to restore product');
                });
            }
        }
    })
</script>
