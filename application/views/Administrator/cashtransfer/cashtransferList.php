<style>
    .v-select {
        margin: 0 10px 5px 5px;
        float: right;
        min-width: 180px;
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

<div id="cashtransferList">
    <div class="row" style="border-bottom: 1px solid #ccc;">
        <div class="col-md-12">
            <form class="form-inline" @submit.prevent="getTransfers">
                <div class="form-group">
                    <label>Transfer To</label>
                    <v-select v-bind:options="branches" v-model="selectedBranch" label="Brunch_name" placeholder="Select Branch"></v-select>
                </div>

                <div class="form-group">
                    <label>Date from</label>
                    <input type="date" class="form-control" v-model="filter.dateFrom">
                </div>

                <div class="form-group">
                    <label>to</label>
                    <input type="date" class="form-control" v-model="filter.dateTo">
                </div>

                <div class="form-group">
                    <input type="submit" class="btn btn-info btn-xs" value="Search" style="padding-top:0px;padding-bottom:0px;margin-top:-4px;">
                </div>
            </form>
        </div>
    </div>

    <div class="row" style="margin-top: 15px;">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Transfer Date</th>
                            <th>Transfer by</th>
                            <th>Transfer To</th>
                            <th>Amount</th>
                            <th>Note</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody style="display:none;" :style="{display: cashtransfers.length > 0 ? '' : 'none'}">
                        <tr v-for="(transfer, sl) in cashtransfers">
                            <td>{{ sl + 1 }}</td>
                            <td>{{ transfer.transferDate }}</td>
                            <td>{{ transfer.AddBy }}</td>
                            <td>{{ transfer.reciveBranchname }}</td>
                            <td>{{ transfer.transferAmount }}</td>
                            <td>{{ transfer.desciption }}</td>
                            <td>
                                <a href="" @click.prevent="approveTransfer(transfer.id)" v-if="transfer.Status == 'p'" title="Approved" class="text-danger"><i class="fa fa-product-hunt"></i></a>
                                <a v-else title="Approved Success" class="text-success"><i class="fa fa-check-circle-o"></i></a>
                                <a href="" :href="`/cash_transfer/${transfer.id}`" target="_blank" title="Edit"><i class="fa fa-edit"></i></a>
                                <a href="" @click.prevent="deleteTransfer(transfer.id)" title="Delete"><i class="fa fa-trash"></i></a>
                            </td>
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
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#cashtransferList',
        data() {
            return {
                filter: {
                    branchTo: null,
                    branchFrom: null,
                    dateFrom: moment().format('YYYY-MM-DD'),
                    dateTo: moment().format('YYYY-MM-DD')
                },
                branches: [],
                selectedBranch: null,
                cashtransfers: []
            }
        },
        created() {
            this.getBranches();
        },
        methods: {
            getBranches() {
                axios.get('/get_branches').then(res => {
                    let thisBranchId = parseInt("<?php echo $this->session->userdata('BRANCHid'); ?>");
                    let ind = res.data.findIndex(branch => branch.brunch_id == thisBranchId);
                    res.data.splice(ind, 1);
                    this.branches = res.data;
                })
            },

            getTransfers() {
                if (this.selectedBranch == null) {
                    alert("Select Branch");
                    return
                }
                this.filter.branchTo = this.selectedBranch.brunch_id;
                this.filter.branchFrom = "<?php echo $this->session->userdata('BRANCHid'); ?>";

                axios.post('/get_cash_transfer', this.filter).then(res => {
                    this.cashtransfers = res.data.message;
                })
            },

            deleteTransfer(transferId) {
                let confirmation = confirm('Are you sure?');
                if (confirmation == false) {
                    return;
                }
                axios.post('/delete_cash_transfer', {
                    id: transferId,
                    Status: 'd'
                }).then(res => {
                    let r = res.data;
                    alert(r);
                    if (r) {
                        this.getTransfers();
                    }
                })
            },

            approveTransfer(transferId) {
                let confirmation = confirm('Are you sure?');
                if (confirmation == false) {
                    return;
                }
                axios.post('/delete_cash_transfer', {
                    id: transferId,
                    Status: 'a'
                }).then(res => {
                    let r = res.data;
                    alert(r);
                    if (r) {
                        this.getTransfers();
                    }
                })
            }
        }
    })
</script>