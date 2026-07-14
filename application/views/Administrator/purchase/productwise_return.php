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
</style>

<div class="row" id="purchase">
    <div class="col-xs-12 col-md-9 col-lg-9">
        <div class="widget-box">
            <div class="widget-header">
                <h4 class="widget-title">Supplier & Product Information</h4>
                <div class="widget-toolbar">
                    <a href="#" data-action="collapse">
                        <i class="ace-icon fa fa-chevron-up"></i>
                    </a>

                    <a href="#" data-action="close">
                        <i class="ace-icon fa fa-times"></i>
                    </a>
                </div>
            </div>

            <div class="widget-body">
                <div class="widget-main">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-xs-4 control-label no-padding-right"> Supplier </label>
                                <div class="col-xs-7">
                                    <v-select v-bind:options="suppliers" v-model="selectedSupplier" v-on:input="onChangeSupplier" label="display_name"></v-select>
                                </div>
                                <div class="col-xs-1" style="padding: 0;">
                                    <a href="<?= base_url('supplier') ?>" title="Add New Supplier" class="btn btn-xs btn-danger" style="height: 25px; border: 0; width: 27px; margin-left: -10px;" target="_blank"><i class="fa fa-plus" aria-hidden="true" style="margin-top: 5px;"></i></a>
                                </div>
                            </div>

                            <div class="form-group" style="display:none;" v-bind:style="{display: selectedSupplier.Supplier_Type == 'G' || selectedSupplier.Supplier_Type == 'N' ? '' : 'none'}">
                                <label class="col-xs-4 control-label no-padding-right"> Name </label>
                                <div class="col-xs-8">
                                    <input type="text" placeholder="Supplier Name" class="form-control" v-model="selectedSupplier.Supplier_Name" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-4 control-label no-padding-right"> Mobile No </label>
                                <div class="col-xs-8">
                                    <input type="text" placeholder="Mobile No" class="form-control" v-model="selectedSupplier.Supplier_Mobile" v-bind:disabled="selectedSupplier.Supplier_Type == 'G' || selectedSupplier.Supplier_Type == 'N' ? false : true" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-4 control-label no-padding-right"> Address </label>
                                <div class="col-xs-8">
                                    <textarea class="form-control" v-model="selectedSupplier.Supplier_Address" v-bind:disabled="selectedSupplier.Supplier_Type == 'G' || selectedSupplier.Supplier_Type == 'N' ? false : true"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <form v-on:submit.prevent="addToCart">
                                <div class="form-group">
                                    <label class="col-xs-4 control-label no-padding-right"> Product </label>
                                    <div class="col-xs-7">
                                        <v-select v-bind:options="products" v-model="selectedProduct" label="display_text" v-on:input="onChangeProduct"></v-select>
                                    </div>
                                    <div class="col-xs-1" style="padding: 0;">
                                        <a href="<?= base_url('product') ?>" title="Add New Product" class="btn btn-xs btn-danger" style="height: 25px; border: 0; width: 27px; margin-left: -10px;" target="_blank"><i class="fa fa-plus" aria-hidden="true" style="margin-top: 5px;"></i></a>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-xs-4 control-label no-padding-right"> Exp. Date </label>
                                    <div class="col-xs-8">
                                        <input type="date" id="exp_date" ref="exp_date" name="exp_date" class="form-control" v-model="selectedProduct.exp_date" @change="onChangeDate" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-xs-4 control-label no-padding-right"> Pur. Rate </label>
                                    <div class="col-xs-3">
                                        <input type="text" id="purchaseRate" name="purchaseRate" class="form-control" placeholder="Pur. Rate" v-model="selectedProduct.Product_Purchase_Rate" v-on:input="productTotal" required />
                                    </div>

                                    <label class="col-xs-2 control-label no-padding-right"> Quantity </label>
                                    <div class="col-xs-3">
                                        <input type="text" step="0.01" id="quantity" name="quantity" class="form-control" placeholder="Quantity" ref="quantity" v-model="selectedProduct.quantity" v-on:input="productTotal" required />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-xs-4 control-label no-padding-right"> Total </label>
                                    <div class="col-xs-8">
                                        <input type="text" id="productTotal" name="productTotal" class="form-control" readonly v-model="selectedProduct.total" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <button type="submit" class="pull-right">Add Cart</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xs-12 col-md-12 col-lg-12" style="padding-left: 0px;padding-right: 0px;">
            <div class="table-responsive">
                <table class="table table-bordered" style="color:#000;margin-bottom: 5px;">
                    <thead>
                        <tr>
                            <th style="width:4%;color:#000;">SL</th>
                            <th style="width:13%;color:#000;">Exp.Date</th>
                            <th style="width:20%;color:#000;">Product Name</th>
                            <th style="width:13%;color:#000;">Category</th>
                            <th style="width:8%;color:#000;">Rate</th>
                            <th style="width:5%;color:#000;">Quantity</th>
                            <th style="width:13%;color:#000;">Total</th>
                            <th style="width:20%;color:#000;">Action</th>
                        </tr>
                    </thead>
                    <tbody style="display:none;" v-bind:style="{display: cart.length > 0 ? '' : 'none'}">
                        <tr v-for="(product, sl) in cart">
                            <td>{{ sl + 1}}</td>
                            <td>{{ product.exp_date }}</td>
                            <td>{{ product.name }}</td>
                            <td>{{ product.categoryName }}</td>
                            <td>{{ product.purchaseRate }}</td>
                            <td>{{ product.quantity }}</td>
                            <td>{{ product.total }}</td>
                            <td><a href="" v-on:click.prevent="removeFromCart(sl)"><i class="fa fa-trash"></i></a></td>
                        </tr>

                        <tr>
                            <td colspan="8"></td>
                        </tr>

                        <tr style="font-weight: bold;">
                            <td colspan="5">Note</td>
                            <td colspan="3">Total</td>
                        </tr>

                        <tr>
                            <td colspan="5"><textarea style="width: 100%;font-size:13px;" placeholder="Note" v-model="purchase.note"></textarea></td>
                            <td colspan="3" style="padding-top: 15px;font-size:18px;">{{ purchase.total }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="col-xs-12 col-md-3 col-lg-3">
        <div class="widget-box">
            <div class="widget-header">
                <h4 class="widget-title">Amount Details</h4>
                <div class="widget-toolbar">
                    <a href="#" data-action="collapse">
                        <i class="ace-icon fa fa-chevron-up"></i>
                    </a>

                    <a href="#" data-action="close">
                        <i class="ace-icon fa fa-times"></i>
                    </a>
                </div>
            </div>

            <div class="widget-body">
                <div class="widget-main">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="table-responsive">
                                <table style="color:#000;margin-bottom: 0px;">
                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                <label class="col-xs-12 control-label no-padding-right">Date</label>
                                                <div class="col-xs-12">
                                                    <input type="date" id="date" name="date" class="form-control" v-model="purchase.returnDate" v-bind:disabled="userType == 'u' ? true : false" />
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                <label class="col-xs-12 control-label no-padding-right">Total</label>
                                                <div class="col-xs-12">
                                                    <input type="number" id="total" name="total" class="form-control" v-model="purchase.total" readonly />
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                <div class="col-xs-12">
                                                    <input type="button" class="btn btn-success" value="Save Return" v-on:click="savePurchase" v-bind:disabled="purchaseOnProgress == true ? true : false" style="background: #007ebb !important; border: 0px !important; padding: 6px 3px; width: 100%; margin-top: 4px;">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
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
        el: '#purchase',
        data() {
            return {
                purchase: {
                    returnDate: moment().format('YYYY-MM-DD'),
                    supplierId: '',
                    total: 0,
                    note: ''
                },
                suppliers: [],
                selectedSupplier: {
                    Supplier_SlNo: '',
                    Supplier_Code: '',
                    Supplier_Name: 'General Supplier',
                    display_name: 'General Supplier',
                    Supplier_Mobile: '',
                    Supplier_Address: '',
                    Supplier_Type: 'G'
                },
                products: [],
                selectedProduct: {
                    Product_SlNo: '',
                    Product_Code: '',
                    display_text: 'Select Product',
                    Product_Name: '',
                    Unit_Name: '',
                    quantity: '',
                    Product_Purchase_Rate: '',
                    Product_SellingPrice: 0,
                    total: ''
                },
                cart: [],
                productStock: 0,
                purchaseOnProgress: false,
                userType: '<?php echo $this->session->userdata("accountType") ?>'
            }
        },
        async created() {
            await this.getSuppliers();
            this.getProducts();
        },
        methods: {
            async getSuppliers() {
                await axios.get('/get_suppliers').then(res => {
                    this.suppliers = res.data;
                    this.suppliers.unshift({
                        Supplier_SlNo: '',
                        Supplier_Code: '',
                        Supplier_Name: 'General Supplier',
                        display_name: 'General Supplier',
                        Supplier_Mobile: '',
                        Supplier_Address: '',
                        Supplier_Type: 'G'
                    })
                })
            },
            getProducts() {
                axios.post('/get_products', {
                    isService: 'false'
                }).then(res => {
                    this.products = res.data;
                })
            },
            onChangeSupplier() {
                if (this.selectedSupplier == null) {
                    this.selectedSupplier = {
                        Supplier_SlNo: '',
                        Supplier_Code: '',
                        Supplier_Name: 'General Supplier',
                        display_name: 'General Supplier',
                        Supplier_Mobile: '',
                        Supplier_Address: '',
                        Supplier_Type: 'G'
                    }
                    return;
                }
            },
            async onChangeProduct() {
                if (this.selectedProduct == null) {
                    this.selectedProduct = {
                        Product_SlNo: '',
                        Product_Code: '',
                        display_text: 'Select Product',
                        Product_Name: '',
                        Unit_Name: '',
                        quantity: '',
                        Product_Purchase_Rate: '',
                        Product_SellingPrice: 0,
                        total: ''
                    }
                    return
                }
                if (this.selectedProduct.Product_SlNo != '') {
                    this.productStock = await axios.post('/get_product_stock', {
                        productId: this.selectedProduct.Product_SlNo
                    }).then(res => {
                        return res.data;
                    })
                    this.$refs.exp_date.focus();
                }
            },

            onChangeDate() {
                if (this.selectedProduct == null) {
                    this.selectedProduct = {
                        Product_SlNo: '',
                        Product_Code: '',
                        display_text: 'Select Product',
                        Product_Name: '',
                        Unit_Name: '',
                        quantity: '',
                        Product_Purchase_Rate: '',
                        Product_SellingPrice: 0,
                        total: ''
                    }
                    return
                }
            },

            productTotal() {
                this.selectedProduct.total = parseFloat(this.selectedProduct.quantity * this.selectedProduct.Product_Purchase_Rate).toFixed(2);
            },
            addToCart() {
                if (this.selectedProduct.Product_SlNo == '') {
                    alert('Select product');
                    return;
                }
                if (this.selectedProduct.exp_date == undefined || this.selectedProduct.exp_date == '') {
                    alert('Select Exp. Date');
                    return;
                }
                if (this.selectedProduct.quantity == undefined || this.selectedProduct.quantity == '') {
                    alert('Enter quantity');
                    return;
                }
                if (parseFloat(this.selectedProduct.quantity) > parseFloat(this.productStock)) {
                    alert('Stock unavailable');
                    return;
                }
                let cartInd = this.cart.findIndex(p => (p.productId == this.selectedProduct.Product_SlNo) && (p.exp_date == this.selectedProduct.exp_date) && (p.isFree == this.isFree));
                if (cartInd > -1) {
                    alert('Product exists in cart');
                    return;
                }

                let product = {
                    productId: this.selectedProduct.Product_SlNo,
                    name: this.selectedProduct.Product_Name,
                    categoryId: this.selectedProduct.ProductCategory_ID,
                    categoryName: this.selectedProduct.ProductCategory_Name,
                    purchaseRate: this.selectedProduct.Product_Purchase_Rate,
                    quantity: this.selectedProduct.quantity,
                    total: this.selectedProduct.total,
                    exp_date: this.selectedProduct.exp_date ?? ''
                }

                this.cart.push(product);
                this.clearSelectedProduct();
                this.calculateTotal();
            },
            async removeFromCart(ind) {
                if (this.cart[ind].id) {
                    let stock = await axios.post('/get_product_stock', {
                        productId: this.cart[ind].productId
                    }).then(res => res.data);
                    if (this.cart[ind].quantity > stock) {
                        alert('Stock unavailable');
                        return;
                    }
                }
                this.cart.splice(ind, 1);
                this.calculateTotal();
            },
            clearSelectedProduct() {
                this.selectedProduct = {
                    Product_SlNo: '',
                    Product_Code: '',
                    display_text: 'Select Product',
                    Product_Name: '',
                    Unit_Name: '',
                    quantity: '',
                    Product_Purchase_Rate: '',
                    Product_SellingPrice: 0,
                    total: ''
                }

                this.isFree = 'no';
            },
            calculateTotal() {
                this.purchase.total = this.cart.reduce((prev, curr) => {
                    return prev + parseFloat(curr.total);
                }, 0).toFixed(2);
            },
            savePurchase() {
                if (this.selectedSupplier == null) {
                    alert('Select supplier');
                    return;
                }

                if (this.purchase.purchaseDate == '') {
                    alert('Enter purchase date');
                    return;
                }

                if (this.cart.length == 0) {
                    alert('Cart is empty');
                    return;
                }

                this.purchase.supplierId = this.selectedSupplier.Supplier_SlNo;
                let data = {
                    purchase: this.purchase,
                    cartProducts: this.cart,
                    supplier: this.selectedSupplier
                }
                let url = '/add_productwisePurchaseReturn';

                this.purchaseOnProgress = true;
                axios.post(url, data).then(async res => {
                    let r = res.data;
					if (r.success) {
						let conf = confirm('Success. Do you want to view invoice?');
						if (conf) {
							window.open('/purchase_return_invoice/' + r.id, '_blank');
							await new Promise(r => setTimeout(r, 1000));
							window.location = '/productwisePurchaseReturn';
						} else {
							window.location = '/productwisePurchaseReturn';
						}
					}
                })
            }
        }
    })
</script>