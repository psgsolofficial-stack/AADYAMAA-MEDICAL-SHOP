

<template>
    <section>
        <div class="app-container">
            <Toolbar>
                <template #start>
                    <Breadcrumb
                        :home="home"
                        :model="items"
                        class="p-menuitem-text p-p-1"
                    />
                </template>

                <template #end>
                    <div class="p-mx-2">
                        <Button
                            icon="pi pi-search"
                            class="p-button-primary px-4"
                            @click="openDialog"
                        />
                    </div>
                    <div class="">
                        <Button
                            icon="pi pi-file-excel"
                            class="p-button-success px-4"
                            @click="dt.exportCSV()"
                        />
                    </div>
                </template>
            </Toolbar>
            <div class="m-2 mt-4 mb-4 p-text-center">
                <h5>Stock Expiry Report</h5>
                <p>{{ resultTitle }}</p>
            </div>
            <div class="p-mt-2" >


<template>
    <div class="card">
        <DataTable v-model:selection="selectedProducts" :value="rList" dataKey="id" tableStyle="min-width: 50rem">
            <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
            <Column field="code" header="Code"></Column>
            <Column field="name" header="Name"></Column>
            <Column field="category" header="Category"></Column>
            <Column field="quantity" header="Quantity"></Column>
        </DataTable>
    </div>
</template>


                
                <DataTable
                    :value="rList"
                    :lazy="true"
                     dataKey="id" 
                    v-model:selection="selectedProducts"
                    class="p-datatable-sm p-datatable-striped p-datatable-gridlines"
                    :resizableColumns="true"
                    :loading="loading"
                    :autoLayout="true"
                    scrollHeight="70vh"
                    responsiveLayout="scroll"
                    :scrollable="true"
                    id="maincontent"
                    editMode="cell" 
                    @cell-edit-complete="onCellEditComplete"
                >
                    <template #empty>
                        <div class="p-text-center p-p-3">No records found</div>
                    </template>
                    <Column selectionMode="multiple"  headerStyle="width: 3rem"></Column>
                    <Column field="itemName" header="Product"></Column>
                    <Column field="receiptDate" header="Bill Date"></Column>
                    <Column field="billNo" header="Bill No"> </Column>
                    <Column field="expiryDate" header="Expiry Date"></Column>
                    <Column field="batchNo" header="Batch No"></Column>
                    <Column field="totalUnit" header="Total Unit"></Column>

                    <Column field="tax3" header="Return Qty">
                        <template #body="{ data }">
                            <input 
                                type="number" 
                                v-model="data.tax3" 
                                class="form-control" 
                                style="width: 80px;"
                                min="0"
                                :max="data.totalUnit"
                                @input="handleReturnQtyChange(data, $event)"
                                :disabled="!isProductSelected(data)"
                            />
                        </template>
                    </Column>
                    <Column field="purchasePrice" header="Purchase Price"></Column>
                    <Column field="purchaseDisc" header="Discount"></Column>
                    <Column field="tax1" header="SGST"></Column>
                    <Column field="tax2" header="CGST"></Column>
                    <Column field="subTotal" header="Total"></Column>

                    <Column field="accountTitle" header="Supplier"></Column>
                  
                </DataTable>

                 <Button
                        type="submit"
                        label="Create Return Voucher"
                        icon="pi pi-search"
                        class="p-button-primary"
                        @click="returnVoucher"
                    />
            </div>


            <!-- This datatable is a computed one and invisible and used for printing purpose only-->
            <!--                    class="p-datatable-sm p-datatable-striped p-datatable-gridlines"-->
       

  <div id="invoiceArea" style="display: block;">
    <!-- Header Section -->
    <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px;">
      <h2 style="margin: 0; color: #333;">Return Voucher</h2>
      <h3 style="margin: 5px 0; color: #666;">Supplier: {{ searchFilters.customerName }}</h3>
      <p style="margin: 5px 0; font-size: 14px;">Date: {{ getCurrentFormattedDate() }}</p>
    </div>
    
    <table style="width: 100%; border: 2px;">
      <thead style="background-color: lightblue;">
        <tr>
          <th>Product</th>
          <th>Bill Date</th>
          <th>Bill No</th>
          <th>Expiry </th>
          <th>Batch</th>
          <th>Return Qty</th>
          <th>Purchase Price</th>
           <th>Discount</th>
          <th>SGST</th>
          <th>CGST</th>
          <th>Total</th>

        </tr>
      </thead>
      <tbody>
        <tr v-for="item in selectedProducts" :key="item.id" style="border: 1px solid;">
          <td>{{ item.itemName }}</td>
          <td>{{ item.receiptDate }}</td>
          <td>{{ item.billNo }}</td>
          <td>{{ item.expiryDate }}</td>
          <td>{{ item.batchNo }}</td>
          <td>{{ item.tax3 }}</td>
          <td>{{ item.purchasePrice }}</td>
          <td>{{ item.purchaseDisc }}</td>
          <td>{{ item.tax1 }}</td>
          <td>{{ item.tax2 }}</td>
          <td>{{ item.subTotal }}</td>


        </tr>
        <tr>
            <td colspan="10">Grand Total</td>
            <td>{{ this.getRetTotal() }}</td>
        </tr>
      </tbody>
    </table>
  </div>

            <Dialog
                v-model:visible="productDialog"
                :style="{ width: '50vw'}"
                :maximizable="true"
                position="top"
                class="p-fluid"
            >
                <template #header>
                    <h5 class="p-dialog-titlebar p-dialog-titlebar-icon">
                        <i class="pi pi-search" style="font-size: 1.2rem"></i>
                        {{ dialogTitle }}
                    </h5>
                </template>
                <div class="p-grid">
                    <div class="p-col">
                        <!-- <div class="p-field">
                            <label for="filterStore">Branch</label>
                            <Dropdown
                                v-model="searchFilters.storeID"
                                :options="filterBranch"
                                :filter="true"
                                optionLabel="name"
                                optionValue="id"
                            />
                        </div> -->
                        <div class="p-field">
                          <label for="from">Date From</label>
                         <input type="date" id="from"  v-model="searchFilters.date1" class="form-control">
                        </div>
                        <div class="p-field">
                          <label for="from">Date To</label>
                         <input type="date" id="to"  v-model="searchFilters.date2" class="form-control">
                        </div>
                        <div class="p-field">
                            <label for="type">Supplier</label>
                <AutoComplete
                  :delay="1000"
                  :minLength="3"
                  @item-select="saveProfile($event)"
                  scrollHeight="500px"
                  v-model="searchFilters.customerName"
                  :suggestions="profilerList"
                  placeholder="Search Profile"
                  @complete="searchProfiler($event)"
                  :dropdown="false"
                >
                  <template #item="slotProps">
                    <div>
                      TITLE :
                      <b class="pull-right">
                        {{ slotProps.item.account_title.toUpperCase() }}
                      </b>
                    </div>
                    <div>
                      Email :
                      <span class="pull-right">
                        {{ slotProps.item.email_address }}
                      </span>
                    </div>
                    <div>
                      Contact :
                      <span class="pull-right">
                        {{ slotProps.item.contact_no }}
                      </span>
                    </div>
                    <div>
                      Account Type :
                      <span class="pull-right">
                        {{ slotProps.item.account_type }}
                      </span>
                    </div>
                  </template>
                </AutoComplete>
              </div>
                    </div>
                </div>
                <template #footer>
                    <Button
                        type="submit"
                        label="Search"
                        icon="pi pi-search"
                        class="p-button-primary"
                        @click="loadList"
                    />
                </template>
            </Dialog>
        </div>
    </section>
</template>
<script lang="ts">
import { Options, mixins } from "vue-class-component";
import { computed, ref } from "vue";
import StoreReports from "../../service/StoreReports";
import UtilityOptions from "../../mixins/UtilityOptions";
import UserService from "../../service/UserService.js";
import AutoComplete from "primevue/autocomplete";
import ProfilerService from "../../service/ProfilerService.js";

   
  


interface IBranch {
    name: string;
}

interface IReport {
    productName: string;
    packSize: string;
    stripSize: string;
    batchNo: string;
    qty: string;
    expiryDate: string;
    minStock: string;
    branchDetails: IBranch;

    
}

interface CounterEntry {
  accountID: number;
  accountHead: string;
  amount: number;
  type: string;
}

@Options({
    title: 'Stock Expiry Report',
    components: {AutoComplete},
})


export default class StockExpiryReport extends mixins(UtilityOptions) {
    private userService;
    private userList = [];
    private profilerList = [];

    private profilerService;


    private dt = ref();
    private lists: IReport[] = [];
    private reportService;
    private resultTitle = "";
    private productDialog = false;
    private loading = false;
    private home = { icon: "pi pi-home", to: "/" };
    private items = [
        { label: "Reports", to: "reports" },
        { label: "Stock Expiry Report", to: "expiry-report" },
    ];

    private searchFilters = {
        id: "",
        storeID: 0,
        date1:"",
        date2:"",
        customerName:"",
        customerID:"",
    };
    private dialogTitle;
    private submitted = false;
    private filterBranch = [];
    private selectedProducts =[];
    private counterEntry: CounterEntry[] = [];

    // private counterEntry = [];




    //CALLING WHENEVER COMPONENT LOADS
    created() {
        this.reportService = new StoreReports();
        this.userService = new UserService();
        this.profilerService = new ProfilerService();


    }

    //CALLNING AFTER CONSTRUCTOR GET CALLED
    mounted() {
        this.storeList();
        this.loadList();
    }

    //OPEN DIALOG TO ADD NEW ITEM
    openDialog() {
        this.submitted = false;
        this.dialogTitle = "Filter Report";
        this.productDialog = true;
    }

    storeList() {
        this.reportService.getFilterList().then((res) => {
            this.filterBranch = res.stores;
        });
    }

    // USED TO GET SEARCHED ASSOCIATE
    loadList() {
        this.loading = true;
        this.reportService.stockExpiryReport(this.searchFilters).then((res) => {
            const data = this.camelizeKeys(res);
           //alert(JSON.stringify(data));
            this.resultTitle = data.resultTitle;
            this.lists = data.record.map(item => ({
                ...item,totalUnit2 :0
            }));
            this.loading = false;
        });
        this.productDialog = false;
    }

    //used to create return voucher
    returnVoucher(){
        console.log('type of selectedProducts: '+typeof this.selectedProducts);
        alert('returning items '+JSON.stringify(this.selectedProducts));
        //document.getElementById("billNo").textContent=res.rno;
        let returnList = this.selectedProducts;
        let supplierID =this.searchFilters.customerID;
        let total = this.getRetTotal();
        // alert('Total to return: '+total);
        console.log("Return List: "+returnList);
        this.counterEntry.push({
            accountID: 2,
            accountHead: 'Expiry Return',
            amount: total,
            type: "Debit",
          });
        //call to save the return voucher
       this.reportService.saveReturnVoucher(null, supplierID, total, returnList, this.counterEntry).then((res) => {
            //const data = this.camelizeKeys(res);
            //console.log("Return Voucher No: "+data.rno);
            

            //print the voucher
            var a = window.open('', '', 'height=500, width=500');
            a?.document.write(document.getElementById('invoiceArea')?.innerHTML);
           a?.print();
          
            });
        
    }

    

     onCellEditComplete = (event) => {
        
        let { data, newValue, field, index } = event;
        
        // Check if this is Return Qty field
        if (field === 'tax3') {
            // Check if this product is selected (checkbox checked)
            const isProductSelected = this.selectedProducts.some(selected => 
                selected.id === data.id || 
                (selected.batchNo === data.batchNo && selected.productName === data.productName)
            );
            
            if (!isProductSelected) {
                // Product not selected, don't allow editing Return Qty
                this.$toast.error('Please select the product first to enter return quantity');
                data[field] = 0; // Reset to 0
                return;
            }
            
            // Validate return quantity doesn't exceed total units
            if (newValue > data.totalUnit) {
                this.$toast.error('Return quantity cannot exceed total units');
                data[field] = data.totalUnit;
                newValue = data.totalUnit;
            }
        }
        
        console.log("Data is "+JSON.stringify(data));
        console.log("new value "+newValue);
        data[field]=newValue;
        
        // Update selected products if this product is selected
        const selectedIndex = this.selectedProducts.findIndex(selected => 
            selected.id === data.id || 
            (selected.batchNo === data.batchNo && selected.productName === data.productName)
        );
        
        if (selectedIndex !== -1) {
            this.selectedProducts[selectedIndex][field] = newValue;
            
            // Recalculate total if needed
            if (field === 'tax3') {
                let pp = this.selectedProducts[selectedIndex]['purchasePrice'] || 0;
                let qty = newValue;
                let tax = this.selectedProducts[selectedIndex]['tax1'] || 0;
                let totalPrice = (pp * qty) * ((100 + 2 * tax) / 100);
                this.selectedProducts[selectedIndex]['subTotal'] = Math.round(totalPrice);
            }
        }
        
        this.rList[index][field] = newValue;
}

    get rList() {
        const l: IReport[] = [];

        this.lists.forEach((e) => {
           // e.expiryDate = this.formatMonthDate(e.expiryDate);
            l.push(e);
        });

        return l;
    }

    searchProfiler(event) {
    setTimeout(() => {
      this.profilerService.searchProfiler(event.query.trim()).then((data) => {
        this.profilerList = data.records;
      });
    }, 200);
  }

  saveProfile(event) {
    const profileInfo = event.value;
    this.searchFilters.customerName = profileInfo.account_title;
    this.searchFilters.customerID = profileInfo.id;
  }


   isProductSelected(data) {
        return this.selectedProducts.some(selected => 
            selected.id === data.id || selected.batchNo === data.batchNo
        );
    }

    handleReturnQtyChange(data, event) {
        const value = Number(event.target.value);
        
        // Instant validation - Check if exceeds total units
        if (value > data.totalUnit) {
            // Show error popup
            alert(`Return quantity not acceptable! Total unit is ${data.totalUnit}, you entered ${value}`);
            
            // Reset to maximum allowed value
            data.tax3 = data.totalUnit;
            event.target.value = data.totalUnit;
            
            // Force focus back to input to show the corrected value
            setTimeout(() => {
                event.target.focus();
                event.target.select();
            }, 100);
            
            return;
        }
        
        // If value is valid, update the data
        data.tax3 = value;
        
        // Update selected products array
        const selectedIndex = this.selectedProducts.findIndex(selected => 
            selected.id === data.id || selected.batchNo === data.batchNo
        );
        
        if (selectedIndex !== -1) {
            this.selectedProducts[selectedIndex].tax3 = value;
            
            // Recalculate and round the subTotal
            let pp = this.selectedProducts[selectedIndex]['purchasePrice'] || 0;
            let tax = this.selectedProducts[selectedIndex]['tax1'] || 0;
            let totalPrice = (pp * value) * ((100 + 2 * tax) / 100);
            this.selectedProducts[selectedIndex]['subTotal'] = Math.round(totalPrice);
        }
    }

   getCurrentFormattedDate() {
        const now = new Date();
        const day = now.getDate();
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const month = months[now.getMonth()];
        const year = now.getFullYear();
        
        return `${day}${month}-${year}`;
    }

   getRetTotal(){
    let total =0;
    //this.$refs.selectedProducts.forEach(element => 
    if(this.selectedProducts!=null)   
        console.log(this.selectedProducts.length);
        console.log( this.selectedProducts.constructor.name);

    this.selectedProducts.forEach((item, index) => {
        // has access to outer scope `parentMessage`
        // but `item` and `index` are only available in here
        console.log(item.subTotal);
        // console.log(item[index].subTotal)
        total += Number(item.subTotal);
    })

    return Math.round(total);

   }
}



</script>

