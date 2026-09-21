<style>
    #smsSettings .provider-card { border: 1px solid #ddd; border-radius: 4px; padding: 15px; margin-bottom: 15px; }
    #smsSettings .provider-card.is-default { border-color: #5cb85c; background: #f6fff6; }
    #smsSettings .provider-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
    #smsSettings .label-default { background: #5cb85c; }
    #smsSettings .test-result { margin-top: 8px; font-size: 12px; white-space: pre-wrap; }
</style>

<div id="smsSettings">
    <div class="row">
        <div class="col-md-6">
            <h4>General</h4>
            <div class="form-group">
                <label class="control-label" for="senderName">Sender Name (used in SMS footer)</label>
                <input type="text" id="senderName" class="form-control" v-model="general.sender_name">
            </div>
            <div class="form-group">
                <label class="control-label" for="senderPhone">Sender Phone (used in SMS footer)</label>
                <input type="text" id="senderPhone" class="form-control" v-model="general.sender_phone">
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-primary btn-sm" v-on:click="saveGeneral" v-bind:disabled="generalSaving">
                    {{ generalSaving ? 'Saving...' : 'Save General Settings' }}
                </button>
            </div>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-8">
            <h4>SMS Providers</h4>
            <p class="help-block">You can configure multiple providers at the same time. Mark one as <b>Default</b> &mdash; it will be used automatically whenever SMS is sent, unless a different provider is explicitly chosen.</p>

            <div class="provider-card" v-for="provider in providers" v-bind:key="provider.provider_key" v-bind:class="{'is-default': provider.is_default == 1}">
                <div class="provider-header">
                    <div>
                        <strong>{{ provider.provider_name || provider.provider_key }}</strong>
                        <span class="label label-default" v-if="provider.is_default == 1" style="margin-left:8px;">Default</span>
                        <span class="label label-warning" v-if="provider.is_enabled != 1" style="margin-left:8px;">Disabled</span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-xs btn-success" v-if="provider.is_default != 1" v-on:click="setDefault(provider)">Set as Default</button>
                        <button type="button" class="btn btn-xs btn-default" v-on:click="editProvider(provider)">Edit</button>
                        <button type="button" class="btn btn-xs btn-danger" v-on:click="removeProvider(provider)">Delete</button>
                    </div>
                </div>
            </div>

            <p v-if="providers.length == 0" class="help-block">No providers configured yet. Add one below.</p>

            <button type="button" class="btn btn-primary btn-sm" v-on:click="startNewProvider" v-if="!editing">
                <i class="fa fa-plus"></i> Add Provider
            </button>

            <div v-if="editing" class="provider-card" style="margin-top:15px;">
                <h4>{{ isNewProvider ? 'Add Provider' : 'Edit Provider' }}</h4>

                <div class="form-group">
                    <label class="control-label">Provider Type</label>
                    <select class="form-control" v-model="form.provider_key" v-bind:disabled="!isNewProvider" v-on:change="onProviderTypeChange">
                        <option value="">-- Select --</option>
                        <option value="gateway1">Gateway 1</option>
                        <option value="mram">MRAM</option>
                        <option value="gateway2">Gateway 2</option>
                        <option value="gennet">GenNet</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="control-label">Display Name</label>
                    <input type="text" class="form-control" v-model="form.provider_name">
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" v-model="form.is_enabled" v-bind:true-value="1" v-bind:false-value="0"> Enabled
                    </label>
                </div>

                <!-- Gateway 1 -->
                <div v-if="form.provider_key == 'gateway1'">
                    <div class="form-group">
                        <label class="control-label">API Key</label>
                        <input type="text" class="form-control" v-model="form.api_key">
                    </div>
                    <div class="form-group">
                        <label class="control-label">SMS Type</label>
                        <input type="text" class="form-control" v-model="form.sms_type">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Sender ID</label>
                        <input type="text" class="form-control" v-model="form.sender_id">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Single SMS URL</label>
                        <input type="text" class="form-control" v-model="form.url">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Bulk SMS URL</label>
                        <input type="text" class="form-control" v-model="form.bulk_url">
                    </div>
                </div>

                <!-- MRAM -->
                <div v-if="form.provider_key == 'mram'">
                    <div class="form-group">
                        <label class="control-label">API Key</label>
                        <input type="text" class="form-control" v-model="form.api_key">
                    </div>
                    <div class="form-group">
                        <label class="control-label">SMS Type</label>
                        <input type="text" class="form-control" v-model="form.sms_type">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Sender ID</label>
                        <input type="text" class="form-control" v-model="form.sender_id">
                    </div>
                    <div class="form-group">
                        <label class="control-label">SMS URL</label>
                        <input type="text" class="form-control" v-model="form.url">
                    </div>
                </div>

                <!-- Gateway 2 -->
                <div v-if="form.provider_key == 'gateway2'">
                    <div class="form-group">
                        <label class="control-label">User ID</label>
                        <input type="text" class="form-control" v-model="form.user_id">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Password</label>
                        <input type="text" class="form-control" v-model="form.password">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Sender ID</label>
                        <input type="text" class="form-control" v-model="form.sender_id_2">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Country Code</label>
                        <input type="text" class="form-control" v-model="form.country_code">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Single SMS URL</label>
                        <input type="text" class="form-control" v-model="form.url_2">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Bulk SMS URL</label>
                        <input type="text" class="form-control" v-model="form.bulk_url_2">
                    </div>
                </div>

                <!-- GenNet -->
                <div v-if="form.provider_key == 'gennet'">
                    <div class="form-group">
                        <label class="control-label">API Token</label>
                        <input type="text" class="form-control" v-model="form.api_key">
                    </div>
                    <div class="form-group">
                        <label class="control-label">SID</label>
                        <input type="text" class="form-control" v-model="form.sender_id">
                        <p class="help-block">Sender/brand masking ID provided by GenNet.</p>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Single SMS URL</label>
                        <input type="text" class="form-control" v-model="form.url" placeholder="https://<domain>/api/v3/send-sms">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Bulk SMS URL</label>
                        <input type="text" class="form-control" v-model="form.bulk_url" placeholder="https://<domain>/api/v3/send-sms/bulk">
                    </div>
                </div>

                <div class="form-group" style="margin-top:15px;">
                    <button type="button" class="btn btn-primary" v-on:click="saveProvider" v-bind:disabled="providerSaving">
                        {{ providerSaving ? 'Saving...' : 'Save Provider' }}
                    </button>
                    <button type="button" class="btn btn-default" v-on:click="cancelEdit">Cancel</button>
                </div>

                <hr>

                <div v-if="!isNewProvider">
                    <label class="control-label">Send Test SMS</label>
                    <div class="input-group" style="max-width:320px;">
                        <input type="text" class="form-control" placeholder="Mobile number" v-model="testNumber">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-info" v-on:click="sendTest" v-bind:disabled="testSending">
                                {{ testSending ? 'Sending...' : 'Test' }}
                            </button>
                        </span>
                    </div>
                    <div class="test-result" v-if="testResult" v-bind:style="{color: testResult.success ? '#3c763d' : '#a94442'}">
                        {{ testResult.success ? 'SUCCESS: ' : 'FAILED: ' }}{{ testResult.message }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>

<script>
    new Vue({
        el: '#smsSettings',
        data(){
            return {
                general: { sender_name: '', sender_phone: '' },
                generalSaving: false,

                providers: [],
                editing: false,
                isNewProvider: true,
                providerSaving: false,
                form: this.emptyForm(),

                testNumber: '',
                testSending: false,
                testResult: null
            }
        },
        created(){
            this.getGeneral();
            this.getProviders();
        },
        methods:{
            emptyForm(){
                return {
                    provider_key: '',
                    provider_name: '',
                    is_enabled: 1,
                    api_key: '',
                    sms_type: '',
                    url: '',
                    bulk_url: '',
                    url_2: '',
                    bulk_url_2: '',
                    sender_id: '',
                    sender_id_2: '',
                    user_id: '',
                    password: '',
                    country_code: ''
                };
            },
            getGeneral(){
                axios.get('/get_sms_settings').then(res => {
                    if(res.data){
                        Object.assign(this.general, res.data);
                    }
                });
            },
            saveGeneral(){
                this.generalSaving = true;
                axios.post('/save_sms_settings', this.general).then(res => {
                    alert(res.data.message);
                    this.generalSaving = false;
                }).catch(() => {
                    this.generalSaving = false;
                    alert('Failed to save general settings');
                });
            },
            getProviders(){
                axios.get('/get_sms_providers').then(res => {
                    this.providers = res.data || [];
                });
            },
            defaultNameFor(key){
                let names = {gateway1: 'Gateway 1', mram: 'MRAM', gateway2: 'Gateway 2', gennet: 'GenNet'};
                return names[key] || key;
            },
            onProviderTypeChange(){
                if(!this.form.provider_name){
                    this.form.provider_name = this.defaultNameFor(this.form.provider_key);
                }
            },
            startNewProvider(){
                this.form = this.emptyForm();
                this.isNewProvider = true;
                this.editing = true;
                this.testResult = null;
            },
            editProvider(provider){
                this.form = Object.assign(this.emptyForm(), provider);
                this.isNewProvider = false;
                this.editing = true;
                this.testResult = null;
                this.testNumber = '';
            },
            cancelEdit(){
                this.editing = false;
                this.testResult = null;
            },
            saveProvider(){
                if(!this.form.provider_key){
                    alert('Select a provider type');
                    return;
                }
                this.providerSaving = true;
                axios.post('/save_sms_provider', this.form).then(res => {
                    alert(res.data.message);
                    this.providerSaving = false;
                    if(res.data.success){
                        this.editing = false;
                        this.getProviders();
                    }
                }).catch(() => {
                    this.providerSaving = false;
                    alert('Failed to save provider');
                });
            },
            setDefault(provider){
                axios.post('/set_default_sms_provider', {provider_key: provider.provider_key}).then(res => {
                    alert(res.data.message);
                    this.getProviders();
                });
            },
            removeProvider(provider){
                if(!confirm('Delete provider "' + (provider.provider_name || provider.provider_key) + '"?')){
                    return;
                }
                axios.post('/delete_sms_provider', {provider_key: provider.provider_key}).then(res => {
                    alert(res.data.message);
                    this.editing = false;
                    this.getProviders();
                });
            },
            sendTest(){
                if(!this.testNumber){
                    alert('Enter a mobile number');
                    return;
                }
                this.testSending = true;
                this.testResult = null;
                axios.post('/test_sms_provider', {provider_key: this.form.provider_key, number: this.testNumber}).then(res => {
                    this.testResult = res.data;
                    this.testSending = false;
                }).catch(() => {
                    this.testSending = false;
                    this.testResult = {success: false, message: 'Request failed'};
                });
            }
        }
    })
</script>
