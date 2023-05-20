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
<div id="cashtransfer">
    <div class="row" style="margin-top: 15px;">
        <div class="col-md-8">
            <form class="form-horizontal" @submit.prevent="addCashTransfer">
                <div class="form-group">
                    <label class="col-lg-6 control-label no-padding-right"> Transfer Date </label>
                    <label class="col-lg-1 control-label no-padding-right">:</label>
                    <div class="col-lg-5">
                        <input type="date" placeholder="Date" class="form-control" v-model="cashtransfer.transferDate" required />
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-lg-6 control-label no-padding-right"> Transfer To </label>
                    <label class="col-lg-1 control-label no-padding-right">:</label>
                    <div class="col-lg-5">
                        <v-select v-bind:options="branches" label="Brunch_name" v-model="selectedBranch" placeholder="Select Branch"></v-select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-lg-6 control-label no-padding-right"> Transfer Amount </label>
                    <label class="col-lg-1 control-label no-padding-right">:</label>
                    <div class="col-lg-5">
                        <input type="number" step="0.01" min="0" placeholder="amount" class="form-control" v-model="cashtransfer.transferAmount" required />
                    </div>
                </div>



                <div class="form-group">
                    <label class="col-lg-6 control-label no-padding-right"> Description </label>
                    <label class="col-lg-1 control-label no-padding-right">:</label>
                    <div class="col-lg-5">
                        <textarea class="form-control" placeholder="Description" v-model="cashtransfer.description"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-lg-6 control-label no-padding-right"></label>
                    <label class="col-lg-1 control-label no-padding-right"></label>
                    <div class="col-lg-5">
                        <button type="submit" class="btn btn-sm btn-success">
                            Submit
                            <i class="ace-icon fa fa-arrow-right icon-on-right bigger-110"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <div style="border: 1px solid gray;width: 250px;margin-top: 35px;padding: 10px 0;">
                <h1 class="text-center" style="margin: 0;border-bottom: 1px dashed lightslategray;">Current Cash</h1>
                <h3 v-if="parseFloat(balance) < 0" class="text-center text-danger" style="margin: 0;">{{parseFloat(balance).toFixed(2)}}</h3>
                <h3 v-else class="text-center text-success" style="margin: 0;">{{parseFloat(balance).toFixed(2)}}</h3>
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
        el: '#cashtransfer',
        data() {
            return {
                cashtransfer: {
                    id: "<?php echo $id; ?>",
                    transferDate: moment().format("YYYY-MM-DD"),
                    transferAmount: 0.00,
                    transferFrom: "<?php echo $this->session->userdata('BRANCHid'); ?>",
                    transferTo: "",
                    description: "",
                },
                branches: [],
                selectedBranch: null,
                balance: 0.00,
                currentBranch: "<?php echo $this->session->userdata('BRANCHid'); ?>"
            }
        },

        created() {
            this.getBranchTo();
            this.getCashBanlance();
            if (this.cashtransfer.id != '') {
                this.getCashTransfer();
            }
        },

        methods: {
            getCashBanlance() {
                axios.post("/get_cash_and_bank_balance", {
                        date: ''
                    })
                    .then(res => {
                        this.balance = res.data.cashBalance.cash_balance;
                    })
            },

            getBranchTo() {
                axios.get("/get_branches")
                    .then(res => {
                        this.branches = res.data.filter(br => br.brunch_id != this.currentBranch)
                    })
            },

            addCashTransfer() {
                if (parseFloat(this.cashtransfer.transferAmount) < 0) {
                    alert("Amount must be grather than 0")
                    return
                }

                if (this.selectedBranch == null) {
                    alert("Select Branch")
                    return
                }

                if (parseFloat(this.balance) <= 0 && parseFloat(this.balance) <= parseFloat(this.cashtransfer.transferAmount)) {
                    alert("Unavailable Cash Balance")
                    return
                }

                this.cashtransfer.transferTo = this.selectedBranch.brunch_id

                let url;
                if (this.cashtransfer.id != '') {
                    url = "/update_cash_transfer"
                } else {
                    url = "/add_cash_transfer";
                }

                axios.post(url, this.cashtransfer)
                    .then(res => {
                        if (res.data.status) {
                            alert(res.data.message)
                            if (this.cashtransfer.id != "") {
                                location.href = "/cash_transfer";
                            }
                            this.clearData();
                        }
                    })
            },

            getCashTransfer() {
                axios.post("/get_cash_transfer", {
                        id: this.cashtransfer.id
                    })
                    .then(res => {
                        let data = res.data.message[0];
                        if (res.data.status && data != undefined) {
                            this.cashtransfer = {
                                id: data.id,
                                transferDate: data.transferDate,
                                transferAmount: data.transferAmount,
                                transferFrom: data.transferFrom,
                                transferTo: data.transferTo,
                                description: data.description,
                                Status: data.Status
                            }

                            this.selectedBranch = {
                                brunch_id: data.transferTo,
                                Brunch_name: data.reciveBranchname
                            }
                        }

                    })
            },

            clearData() {
                this.cashtransfer = {
                    id: "",
                    transferDate: moment().format("YYYY-MM-DD"),
                    transferAmount: 0.00,
                    transferFrom: "<?php echo $this->session->userdata('BRANCHid'); ?>",
                    transferTo: "",
                    description: "",
                }

                this.selectedBranch = null
            }
        },
    })
</script>