<style>
    tr td,
    tr th {
        vertical-align: middle !important;
    }
</style>
<div id="saleSlab">
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="col-xs-12 col-md-8 col-md-offset-2">
                <div class="row" style="border-radius: 5px; border: 2px solid #007ebb; margin: 0px 0px 10px; padding: 7px 0px;">
                    <h3 style="margin: 0; padding-left: 12px;margin-bottom: 10px;border-bottom: 1px solid gray;padding-bottom: 5px;">Sale Slab Information</h3>
                    <form @submit.prevent="addSaleSlab">
                        <div class="form-group">
                            <label for="" class="col-xs-4 col-md-4">Slab Title:</label>
                            <div class="col-xs-8 col-md-8">
                                <input type="text" class="form-control" v-model="slab.name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-xs-4 col-md-4">DateFrom:</label>
                            <div class="col-xs-8 col-md-3">
                                <input type="date" class="form-control" v-model="slab.dateFrom">
                            </div>
                            <label for="" class="col-xs-4 col-md-1">DateTo:</label>
                            <div class="col-xs-8 col-md-4">
                                <input type="date" class="form-control" v-model="slab.dateTo">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-xs-4 col-md-4">Amount:</label>
                            <div class="col-xs-8 col-md-8">
                                <input type="number" step="any" min="0" id="amount" class="form-control" v-model="slab.amount">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-xs-4 col-md-4">Discount:</label>
                            <div class="col-xs-8 col-md-8">
                                <input type="number" step="any" min="0" id="discount" class="form-control" v-model="slab.discount">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-xs-4 col-md-4"></label>
                            <div class="col-xs-8 col-md-8 text-right">
                                <button class="btn btn-success" type="submit"><span v-html="slab.id != '' ? 'Update' : 'Save'"></span> Slab</button>
                            </div>
                        </div>
                    </form>
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
                <datatable :columns="columns" :data="slabs" :filter-by="filter">
                    <template scope="{ row }">
                        <tr>
                            <td>{{ row.sl }}</td>
                            <td>{{ row.name }}</td>
                            <td>{{ row.start_date }}</td>
                            <td>{{ row.end_date }}</td>
                            <td>{{ row.amount }}</td>
                            <td>{{ row.discount }}</td>
                            <td>
                                <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                    <button type="button" class="button edit" @click="editSlab(row)">
                                        <i class="ri-edit-2-line"></i>
                                    </button>
                                    <button type="button" class="button" @click="deleteSlab(row.id)">
                                        <i class="ri-delete-bin-line"></i>
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
        el: '#saleSlab',
        data() {
            return {
                slab: {
                    id: '',
                    name: '',
                    dateFrom: moment().format('YYYY-MM-DD'),
                    dateTo: moment().format('YYYY-MM-DD'),
                    amount: '',
                    discount: ''
                },
                userType: '<?php echo $this->session->userdata("accountType"); ?>',
                save_disabled: false,

                slabs: [],
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
                        label: 'Date From',
                        field: 'start_date',
                        align: 'center'
                    },
                    {
                        label: 'Date To',
                        field: 'end_date',
                        align: 'center'
                    },
                    {
                        label: 'Amount',
                        field: 'amount',
                        align: 'center'
                    },
                    {
                        label: 'Discount(%)',
                        field: 'discount',
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
            this.getSaleSlabs();
        },
        methods: {
            getSaleSlabs() {
                axios.get('/get_sale_slabs')
                    .then(res => {
                        this.slabs = res.data.map((item, index) => {
                            item.sl = index + 1;
                            return item;
                        });
                    })
            },

            addSaleSlab() {
                if (this.slab.name == '') {
                    alert('Please enter slab title');
                    return;
                }
                if (this.slab.dateFrom == '') {
                    alert('Please select date from');
                    return;
                }
                if (this.slab.dateTo == '') {
                    alert('Please select date to');
                    return;
                }
                if (this.slab.amount == '' || this.slab.amount <= 0) {
                    alert('Please enter amount');
                    return;
                }
                if (this.slab.discount == '' || this.slab.discount <= 0) {
                    alert('Please enter discount');
                    return;
                }

                let data = {
                    slab: this.slab
                }

                let url = '/add_sale_slab';
                if (this.slab.id != '') {
                    url = '/update_sale_slab';
                }

                axios.post(url, data)
                    .then(res => {
                        if (res.data.success) {
                            alert(res.data.message);
                            this.slab = {
                                id: '',
                                name: '',
                                dateFrom: moment().format('YYYY-MM-DD'),
                                dateTo: moment().format('YYYY-MM-DD'),
                                amount: '',
                                discount: ''
                            };
                            this.getSaleSlabs();
                        }
                    })
            },

            editSlab(slab) {
                this.slab.id = slab.id;
                this.slab.name = slab.name;
                this.slab.dateFrom = slab.start_date;
                this.slab.dateTo = slab.end_date;
                this.slab.amount = slab.amount;
                this.slab.discount = slab.discount;
            },

            deleteSlab(id) {
                if (confirm('Are you sure to delete this slab program?')) {
                    axios.post('/delete_sale_slab', {
                            slabId: id
                        })
                        .then(res => {
                            if (res.data.success) {
                                alert(res.data.message);
                                this.getSaleSlabs();
                            }
                        })
                }
            }
        }
    })
</script>