<style>
    .v-select {
        margin-bottom: 5px;
    }

    .v-select.open .dropdown-toggle {
        border-bottom: 1px solid #ccc;
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
<div id="daycloses">
    <form @submit.prevent="saveDayClose">
        <div class="row" style="margin-top: 10px;margin-bottom:15px;border-bottom: 1px solid #ccc;padding-bottom:15px;">
            <div class="col-md-6 col-md-offset-3">
                <?php if (!in_array($this->session->userdata('accountType'), ['u', 'e'])): ?>
                    <div class="form-group clearfix">
                        <label class="control-label col-md-4">User:</label>
                        <div class="col-md-7">
                            <v-select :options="users" v-model="selectedUser" label="FullName"></v-select>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Date:</label>
                    <div class="col-md-7">
                        <input type="date" class="form-control" v-model="dayclose.date" required />
                    </div>
                </div>

                <div class="form-group clearfix">
                    <label class="control-label col-md-4">Day Close DateTime:</label>
                    <div class="col-md-7">
                        <input type="datetime-local" class="form-control" step="1" v-model="dayclose.close_date_time" required>
                    </div>
                </div>


                <div class="form-group clearfix">
                    <div class="col-md-7 col-md-offset-4 text-right">
                        <input type="submit" class="btn btn-success btn-sm" value="Save">
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="row">
        <div class="col-sm-12 form-inline">
            <div class="form-group">
                <label for="filter" class="sr-only">Filter</label>
                <input type="text" class="form-control" v-model="filter" placeholder="Filter">
            </div>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <datatable :columns="columns" :data="daycloses" :filter-by="filter" style="margin-bottom: 5px;">
                    <template scope="{ row }">
                        <tr>
                            <td>{{ row.sl }}</td>
                            <td>{{ row.date }}</td>
                            <td>{{ row.close_date_time }}</td>
                            <td>{{ row.FullName }}</td>
                            <td>
                                <?php if (!in_array($this->session->userdata('accountType'), ['u', 'e'])) { ?>
                                    <button type="button" class="button edit" @click="editDayClose(row)">
                                        <i class="ri-edit-2-line"></i>
                                    </button>
                                    <button type="button" class="button" @click="deleteDayClose(row.id)">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                <?php } ?>
                            </td>
                        </tr>
                    </template>
                </datatable>
                <datatable-pager v-model="page" type="abbreviated" :per-page="per_page" style="margin-bottom: 50px;"></datatable-pager>
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
        el: '#daycloses',
        data() {
            return {
                dayclose: {
                    id: 0,
                    date: moment().format("YYYY-MM-DD"),
                    close_date_time: moment().format('YYYY-MM-DD HH:mm:ss'),
                    user_id: '',
                    status: 'a'
                },
                daycloses: [],
                users: [],
                selectedUser: null,
                userType: "<?= $this->session->userdata('accountType'); ?>",

                columns: [{
                        label: 'SlNo.',
                        field: 'sl',
                        align: 'center',
                        filterable: false
                    },
                    {
                        label: 'Date',
                        field: 'date',
                        align: 'center'
                    },
                    {
                        label: 'Close DateTime',
                        field: 'close_date_time',
                        align: 'center'
                    },
                    {
                        label: 'User',
                        field: 'FullName',
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
            this.getUser();
            this.getDayCloses();
        },
        methods: {
            getUser() {
                axios.get('/get_users').then(res => {
                    this.users = res.data;
                    this.selectedUser = this.users.find(u => u.User_SlNo == "<?= $this->session->userdata('userId'); ?>");
                })
            },

            getDayCloses() {
                let data = {
                    userId: ['u', 'e'].includes(this.userType) ? "<?= $this->session->userdata('userId'); ?>" : null
                }

                axios.post('/get_daycloses', data).then(res => {
                    this.daycloses = res.data.map((item, index) => {
                        item.sl = index + 1;
                        return item;
                    });
                })
            },

            saveDayClose() {
                let url = '/add_dayclose';
                if (this.dayclose.id != 0) {
                    url = '/update_dayclose';
                }

                this.dayclose.user_id = this.selectedUser ? this.selectedUser.User_SlNo : '';
                axios.post(url, this.dayclose).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.resetForm();
                        this.getDayCloses();
                    }
                })
            },
            editDayClose(dayclose) {
                let keys = Object.keys(this.dayclose);
                keys.forEach(key => {
                    this.dayclose[key] = dayclose[key];
                })
                this.selectedUser = this.users.find(u => u.User_SlNo == dayclose.user_id);
            },
            deleteDayClose(daycloseId) {
                let deleteConfirm = confirm('Are you sure?');
                if (deleteConfirm == false) {
                    return;
                }
                axios.post('/delete_dayclose', {
                    daycloseId: daycloseId
                }).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.getDayCloses();
                    }
                })
            },
            resetForm() {
                this.dayclose = {
                    id: 0,
                    date: moment().format("YYYY-MM-DD"),
                    close_date_time: moment().format('YYYY-MM-DD HH:mm:ss'),
                    user_id: '',
                    status: 'a'
                }
            }
        }
    })
</script>