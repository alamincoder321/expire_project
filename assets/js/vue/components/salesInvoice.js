const salesInvoice = Vue.component('sales-invoice', {
    template: `
        <div>
            <div class="row">
                <div class="col-xs-12">
                    <a href="" ref="printButton" v-on:click.prevent="print"><i class="fa fa-print"></i> Print</a>
                </div>
            </div>
            
            <div id="invoiceContent">
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <div _h098asdh>
                            {{ sales.is_sample == 'true' ? 'Sample Invoice' : 'Sales Invoice'}}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-7">
                        <strong>Customer Id:</strong> {{ sales.Customer_Code }}<br>
                        {{ sales.Customer_Name }}<br>
                        {{ sales.Customer_Address }}<br>
                        {{ sales.Customer_Mobile }}<br>
                        <span v-if="sales.Project_Name"> <strong>Project:</strong> {{ sales.Project_Name }} </span>
                    </div>
                    <div class="col-xs-5 mobile-second-section">
                        <strong>SalesBy:</strong> {{ sales.AddBy }}<br>
                        <strong>Invoice:</strong> {{ sales.SaleMaster_InvoiceNo }}<br>
                        <strong>Date:</strong> {{ sales.SaleMaster_SaleDate }} {{ sales.AddTime | formatDateTime('h:mm a') }}<br>
                        <span v-if="sales.Employee_Name"> <strong>Employee:</strong> {{ sales.Employee_Name }} </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div _d9283dsc></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div v-if="sales.customerType != 'G'" style="margin-bottom: 10px;">
                            <table _a584de>
                                <thead>
                                <tr>
                                        <td >   <strong>Previous Point:</strong> {{  sales.customerPoint - sales.point + parseInt(sales.pointAmount) }}<br></td>
                                        <td >   <strong>New Point:</strong> {{ sales.point }}<br></td>
                                        <td>  <strong>Used Point:</strong> {{parseInt(sales.pointAmount) }}<br></td>
                                        <td>  <strong>Total Point:</strong> {{sales.customerPoint }}<br></td>                                   
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        
                        <table _a584de>
                            <thead>
                                <tr>
                                    <td>Sl.</td>
                                    <td>Product Description</td>
                                    <td>Quantity</td>
                                    <td>Rate</td>
                                    <td>Total</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(product, sl) in cart">
                                    <td>{{ sl + 1 }}</td>
                                    <td style="text-align:left;" :style="{lineHeight: product.isFree == 'yes' ? '0.9' : ''}">
                                        {{ product.Product_Name }} - {{product.Product_Code}}
                                         <span v-if="product.isFree == 'yes'">
                                            <br>
                                            <i style="font-weight: 700;font-size: 10px;">Free</i>
                                         </span>
                                    </td>
                                    <td>{{ product.SaleDetails_TotalQuantity }} {{ product.Unit_Name }}</td>
                                    <td>{{ product.SaleDetails_Rate }}</td>
                                    <td align="right">{{ product.SaleDetails_TotalAmount }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6 text-left">
                        <div v-if="sales.SaleMaster_bankPaid > 0" style="margin:0; margin-top:10px; border-bottom: 1px solid gray; padding-bottom: 5px;">
                            <table _a584de>
                                <tr>
                                    <td style="font-weight:700;">Sl</td>
                                    <td style="font-weight:700;">Bank Name</td>
                                    <td style="font-weight:700;">Amount</td>
                                </tr>
                                <tr>
                                    <td>1</td>
                                    <td>{{sales.bank_name}} - {{sales.account_number}} - {{sales.account_name}}</td>
                                    <td>{{sales.SaleMaster_bankPaid}}</td>
                                </tr>
                            </table>
                        </div>
                        <br>
                        <strong>In Word: </strong> {{ convertNumberToWords(sales.SaleMaster_TotalSaleAmount) }}
                    </div>
                    <div class="col-xs-6">
                        <table _t92sadbc2>
                            <tr>
                                <td><strong>Sub Total:</strong></td>
                                <td style="text-align:right">{{ sales.SaleMaster_SubTotalAmount }}</td>
                            </tr>
                            <tr>
                                <td><strong>Discount (-):</strong></td>
                                <td style="text-align:right">{{ sales.SaleMaster_TotalDiscountAmount }}</td>
                            </tr>
                            <tr v-show="sales.pointAmount > 0" style="display: none;" :style="{display: sales.pointAmount > 0 ? '' : 'none'}">
                                <td><strong>Point (-):</strong></td>
                                <td style="text-align:right">{{ sales.pointAmount }}</td>
                            </tr>
                            <tr><td colspan="2" style="border-bottom: 1px solid #ccc"></td></tr>
                            <tr>
                                <td><strong>Total:</strong></td>
                                <td style="text-align:right">{{ sales.SaleMaster_TotalSaleAmount }}</td>
                            </tr>
                            <tr>
                                <td><strong>Paid:</strong></td>
                                <td style="text-align:right">{{ sales.SaleMaster_PaidAmount }}</td>
                            </tr>
                            <tr>
                                <td><strong>Return Amount:</strong></td>
                                <td style="text-align:right">{{ sales.returnAmount }}</td>
                            </tr>
                        </table>
                    </div>
                   
                     <div class="col-xs-12">
                      <br/>
                    <br/>
                     Thank you for shopping with. Purchase of defected item must be exchanged by 24 hours with invoice. For any queries, suggestions or complaints, please call: "8809649991010" (7:00 AM - 10:00 PM)</div>
                </div>
            </div>
        </div>
    `,
    props: ['sales_id', 'print_confirm' ],
    data() {
        return {
            sales: {
                SaleMaster_InvoiceNo: null,
                SalseCustomer_IDNo: null,
                SaleMaster_SaleDate: null,
                Customer_Name: null,
                Customer_Address: null,
                Customer_Mobile: null,
                SaleMaster_TotalSaleAmount: null,
                SaleMaster_TotalDiscountAmount: null,
                SaleMaster_TaxAmount: null,
                SaleMaster_Freight: null,
                SaleMaster_SubTotalAmount: null,
                SaleMaster_PaidAmount: null,
                SaleMaster_DueAmount: null,
                SaleMaster_Previous_Due: null,
                SaleMaster_Description: null,
                AddBy: null
            },
            cart: [],
            style: null,
            companyProfile: null,
            currentBranch: null
        }
    },
    filters: {
        formatDateTime(dt, format) {
            return dt == '' || dt == null ? '' : moment(dt).format(format);
        }
    },
    // created() {
    //     this.setStyle();
    //     this.getSales();
    //     this.getCurrentBranch();
    // },
     created() {
        this.setStyle();
        this.getSales();
        this.getCurrentBranch();        
    },
    methods: {
        async getSales() {
            await axios.post('/get_sales', { salesId: this.sales_id }).then(async res => {
                this.sales = res.data.sales[0];
                this.cart = res.data.saleDetails;
                if(this.print_confirm == 1){
                    setTimeout(() => {
                            this.$refs.printButton.click();
                    }, 1000)                        
                }
            })
        },
        getCurrentBranch() {
            axios.get('/get_current_branch').then(res => {
                this.currentBranch = res.data;
            })
        },
        setStyle() {
            this.style = document.createElement('style');
            this.style.innerHTML = `
                div[_h098asdh]{
                    /*background-color:#e0e0e0;*/
                    font-weight: bold;
                    font-size:15px;
                    margin-bottom:15px;
                    padding: 5px;
                    border-top: 1px dotted #454545;
                    border-bottom: 1px dotted #454545;
                }
                div[_d9283dsc]{
                    padding-bottom:25px;
                    border-bottom: 1px solid #ccc;
                    margin-bottom: 15px;
                }
                table[_a584de]{
                    width: 100%;
                    text-align:center;
                }
                table[_a584de] thead{
                    font-weight:bold;
                }
                table[_a584de] td{
                    padding: 3px;
                    border: 1px solid #ccc;
                }
                table[_t92sadbc2]{
                    width: 100%;
                }
                table[_t92sadbc2] td{
                    padding: 2px;
                }
                .mobile-second-section{
                    text-align: right;
                }
            `;
            document.head.appendChild(this.style);
        },
        convertNumberToWords(amountToWord) {
            var words = new Array();
            words[0] = '';
            words[1] = 'One';
            words[2] = 'Two';
            words[3] = 'Three';
            words[4] = 'Four';
            words[5] = 'Five';
            words[6] = 'Six';
            words[7] = 'Seven';
            words[8] = 'Eight';
            words[9] = 'Nine';
            words[10] = 'Ten';
            words[11] = 'Eleven';
            words[12] = 'Twelve';
            words[13] = 'Thirteen';
            words[14] = 'Fourteen';
            words[15] = 'Fifteen';
            words[16] = 'Sixteen';
            words[17] = 'Seventeen';
            words[18] = 'Eighteen';
            words[19] = 'Nineteen';
            words[20] = 'Twenty';
            words[30] = 'Thirty';
            words[40] = 'Forty';
            words[50] = 'Fifty';
            words[60] = 'Sixty';
            words[70] = 'Seventy';
            words[80] = 'Eighty';
            words[90] = 'Ninety';
            amount = amountToWord == null ? '0.00' : amountToWord.toString();
            var atemp = amount.split(".");
            var number = atemp[0].split(",").join("");
            var n_length = number.length;
            var words_string = "";
            if (n_length <= 9) {
                var n_array = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0);
                var received_n_array = new Array();
                for (var i = 0; i < n_length; i++) {
                    received_n_array[i] = number.substr(i, 1);
                }
                for (var i = 9 - n_length, j = 0; i < 9; i++, j++) {
                    n_array[i] = received_n_array[j];
                }
                for (var i = 0, j = 1; i < 9; i++, j++) {
                    if (i == 0 || i == 2 || i == 4 || i == 7) {
                        if (n_array[i] == 1) {
                            n_array[j] = 10 + parseInt(n_array[j]);
                            n_array[i] = 0;
                        }
                    }
                }
                value = "";
                for (var i = 0; i < 9; i++) {
                    if (i == 0 || i == 2 || i == 4 || i == 7) {
                        value = n_array[i] * 10;
                    } else {
                        value = n_array[i];
                    }
                    if (value != 0) {
                        words_string += words[value] + " ";
                    }
                    if ((i == 1 && value != 0) || (i == 0 && value != 0 && n_array[i + 1] == 0)) {
                        words_string += "Crores ";
                    }
                    if ((i == 3 && value != 0) || (i == 2 && value != 0 && n_array[i + 1] == 0)) {
                        words_string += "Lakhs ";
                    }
                    if ((i == 5 && value != 0) || (i == 4 && value != 0 && n_array[i + 1] == 0)) {
                        words_string += "Thousand ";
                    }
                    if (i == 6 && value != 0 && (n_array[i + 1] != 0 && n_array[i + 2] != 0)) {
                        words_string += "Hundred and ";
                    } else if (i == 6 && value != 0) {
                        words_string += "Hundred ";
                    }
                }
                words_string = words_string.split("  ").join(" ");
            }
            return words_string + ' only';
        },
        async print() {
            let invoiceContent = document.querySelector('#invoiceContent').innerHTML;

            let printWindow = window.open('', 'PRINT', `width=${screen.width}, height=${screen.height}, left=0, top=0`);

            printWindow.document.open();
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Invoice</title>
                    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
                    <style>
                        ${this.style.innerHTML}   /* 🔥 CUSTOM STYLE FIX */
                        body, table {
                            font-size: 13px;
                        }
                    </style>
                </head>

                <body onload="window.print()">

                    <div style="text-align:center;">
                        <img src="/uploads/company_profile_thum/${this.currentBranch.Company_Logo_org}" style="height:80px;" /><br>
                        <strong style="font-size:18px;">${this.currentBranch.Company_Name}</strong><br>
                        <p style="white-space:pre-line;">${this.currentBranch.Repot_Heading}</p>
                    </div>

                    ${invoiceContent}

                    <div style="text-align:center;margin-top: 25px;">Print Time: ${moment().format("DD/MM/YYYY h:mm:ss A")}</div>

                    <script>
                        // 🔥 print OR cancel → same behavior
                        window.onafterprint = function() {
                            window.close();
                        };

                        // 🔥 fallback (some browser ignore onafterprint)
                        window.addEventListener('focus', function() {
                            setTimeout(function(){
                                window.close();
                            }, 500);
                        });
                    <\/script>

                </body>
                </html>
            `);

            printWindow.document.close();

            // 🔥 detect window close → redirect
            let timer = setInterval(() => {
                if (printWindow.closed) {
                    clearInterval(timer);

                    if (this.print_confirm == 1) {
                        window.location.href = "/sales/product";
                    }
                }
            }, 500);
        }
    }
})