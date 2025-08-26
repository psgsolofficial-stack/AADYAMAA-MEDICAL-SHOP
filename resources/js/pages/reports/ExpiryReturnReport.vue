<template>
  <div class="app-container">
    <div class="jumbotron p-1 text-center mb-0">
      <h5><i class="pi pi-exclamation-triangle"></i> Expiry Return Report</h5>
      <h6 class="font-weight-bold">View expired products by supplier for return processing</h6>
    </div>

    <div class="card">
      <div class="card-body">
        <!-- Supplier Selection -->
        <div class="row mb-4">
          <div class="col-md-6">
            <label class="form-label">Select Supplier:</label>
            <select 
              v-model="selectedSupplierId" 
              @change="loadExpiryReturnReport" 
              class="form-control"
            >
              <option value="">-- Select Supplier --</option>
              <option 
                v-for="supplier in suppliers" 
                :key="supplier.id" 
                :value="supplier.id"
              >
                {{ supplier.name }}
              </option>
            </select>
          </div>
          <div class="col-md-6" v-if="reportData.total_products">
            <div class="card bg-danger text-white">
              <div class="card-body">
                <h5>{{ reportData.total_products || 0 }}</h5>
                <p>Total Expired Products</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Products List -->
        <div v-if="loading" class="text-center">
          <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
          <p>Loading expired products...</p>
        </div>

        <div v-else-if="reportData.data && reportData.data.bills && reportData.data.bills.length > 0">
          <div class="card">
            <div class="card-header bg-primary text-white">
              <h6 class="mb-0">
                <i class="pi pi-building"></i> {{ reportData.data.supplier_name }}
                <span class="badge badge-light ml-2">{{ reportData.data.bills.length }} Bills</span>
              </h6>
            </div>
            <div class="card-body">
              <!-- Bills List -->
              <div v-for="bill in reportData.data.bills" :key="bill.bill_no" class="mb-3">
                <div class="card border">
                  <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <div>
                      <strong>Bill No: {{ bill.bill_no }}</strong>
                      <span class="ml-3 text-muted">{{ bill.total_products }} Products</span>
                      <span class="ml-3 text-success">Total: Rs.{{ formatAmount(bill.total_value) }}</span>
                    </div>
                    <button 
                      @click="toggleBillDetails(bill.bill_no)" 
                      class="btn btn-sm btn-outline-primary"
                    >
                      {{ expandedBills.includes(bill.bill_no) ? 'Hide Details' : 'View All' }}
                    </button>
                  </div>
                  
                  <!-- Products Details (Collapsible) -->
                  <div v-if="expandedBills.includes(bill.bill_no)" class="card-body p-0">
                    <div class="table-responsive">
                      <table class="table table-striped mb-0">
                        <thead class="thead-light">
                          <tr>
                            <th>Product Name</th>
                            <th>Batch No</th>
                            <th>Expiry Date</th>
                            <th>Quantity</th>
                            <th>Purchase Price</th>
                            <th>Total Value</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-for="product in bill.products" :key="product.id">
                            <td>{{ product.product_name }}</td>
                            <td>{{ product.batch_no }}</td>
                            <td>
                              <span class="badge badge-danger">{{ product.expiry_date }}</span>
                            </td>
                            <td>{{ product.qty }}</td>
                            <td>Rs.{{ formatAmount(product.purchase_price) }}</td>
                            <td>Rs.{{ formatAmount(product.total_value) }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-else-if="selectedSupplierId && !loading" class="text-center py-5">
          <i class="pi pi-check-circle" style="font-size: 3rem; color: green"></i>
          <h5 class="mt-3">No Expired Products Found</h5>
          <p>This supplier has no expired products.</p>
        </div>

        <div v-else-if="!selectedSupplierId" class="text-center py-5">
          <i class="pi pi-info-circle" style="font-size: 3rem; color: #007bff"></i>
          <h5 class="mt-3">Select a Supplier</h5>
          <p>Please select a supplier to view their expired products.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { Options, mixins } from "vue-class-component";
import UtilityOptions from "../../mixins/UtilityOptions";
import axios from "axios";

@Options({
  title: 'Expiry Return Report',
  components: {},
})

export default class ExpiryReturnReport extends mixins(UtilityOptions) {
  private loading = false;
  private suppliers: any[] = [];
  private selectedSupplierId = '';
  private expandedBills: string[] = [];
  private reportData: any = {
    data: null,
    total_products: 0
  };

  mounted() {
    this.loadSuppliers();
  }

  async loadSuppliers() {
    try {
      const response = await axios.get('/api/suppliers');
      
      if (response.data.success) {
        this.suppliers = response.data.data;
      } else {
        this.$toast.error('Failed to load suppliers');
      }
    } catch (error) {
      console.error('Error loading suppliers:', error);
      this.$toast.error('Error loading suppliers');
    }
  }

  async loadExpiryReturnReport() {
    if (!this.selectedSupplierId) {
      this.reportData = { data: null, total_products: 0 };
      this.expandedBills = [];
      return;
    }

    this.loading = true;
    this.expandedBills = [];
    try {
      const response = await axios.post('/api/expiry_return_report', {
        supplier_id: this.selectedSupplierId
      });
      
      if (response.data.success) {
        this.reportData = response.data;
      } else {
        this.$toast.error('Failed to load expiry return report');
      }
    } catch (error) {
      console.error('Error loading expiry return report:', error);
      this.$toast.error('Error loading expiry return report');
    } finally {
      this.loading = false;
    }
  }

  toggleBillDetails(billNo: string) {
    const index = this.expandedBills.indexOf(billNo);
    if (index > -1) {
      this.expandedBills.splice(index, 1);
    } else {
      this.expandedBills.push(billNo);
    }
  }
}
</script>

<style scoped>
.card {
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  border: none;
}

.badge-danger {
  background-color: #dc3545;
}

.badge-light {
  background-color: #f8f9fa;
  color: #495057;
}

.table th {
  border-top: none;
  font-weight: 600;
}

.bg-primary {
  background-color: #007bff !important;
}

.bg-warning {
  background-color: #ffc107 !important;
}

.bg-danger {
  background-color: #dc3545 !important;
}
</style>