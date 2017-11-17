<template>
  <div class="app-container calendar-list-container">
    <div class="filter-container">
      <el-row :gutter="10" type="flex" justify="left">
        <el-button-group>
          <el-button class="top-button-group" type="text"
            @click="handleReCalculate()">重算配送费</el-button>
          <router-link to="/finance/goods-settings" class="el-button filter-item el-button--text">货品设置</router-link>
          <el-button class="top-button-group" type="text"
                     @click="handleExport()"
          >导出</el-button>
          <el-button class="top-button-group" type="text"
                     @click="handlePrint()"
          >打印</el-button>
        </el-button-group>
      </el-row>
      <el-row :gutter='10'>
        <span class="demonstration">订单时间</span>
        <el-date-picker
            v-model="listQuery.add_time"
            type="datetimerange"
            placeholder="选择时间范围"
            :picker-options="addTimePickerOption">
        </el-date-picker>
        <span class="demonstration">档口</span>
        <el-select v-model="listQuery.store_id" filterable placeholder="请输入档口">
          <el-option label="全部" value=""></el-option>
          <el-option
            v-for="item in storeOptions"
            :key="item.store_id"
            :label="item.store_name"
            :value="item.store_id">
          </el-option>
        </el-select>
        <span class="demonstration">支付单号</span>
        <el-input placeholder="支付单号" v-model="listQuery.pay_sn" style="width:200px"></el-input>
        <el-button class="filter-item" type="primary" icon="search"
          @click="handleSearch()">查询</el-button>
        <el-button class="filter-item" type="text"
          @click="handleSearchMore()">更多</el-button>
      </el-row>
      <div :class="{ 'search-more':isSearchMore }">
        <el-row>
          <span class="demonstration">记账状态</span>
          <el-select v-model="listQuery.status">
            <el-option
              v-for="item in statusMap"
              :key="item.value"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
          <span class="demonstration">交款人</span>
          <el-select v-model="listQuery.jk_driver_id" filterable placeholder="请输入交款司机">
            <el-option label="全部" value=""></el-option>
            <el-option
              v-for="item in driverOptions"
              :key="item.id"
              :label="item.name"
              :value="item.id">
            </el-option>
          </el-select>
          <span class="demonstration">记账人</span>
          <el-select v-model="listQuery.jzr" filterable placeholder="请输入记账人">
            <el-option label="全部" value=""></el-option>
            <el-option
              v-for="item in adminOptions"
              :key="item.admin_id"
              :label="item.admin_name"
              :value="item.admin_id">
            </el-option>
          </el-select>
        </el-row>
      </div>
    </div>
    <el-table id="print-wrap"
      :data="list" ref="detailListTable" v-loading.body="listLoading"
      border fit highlight-current-row
      @selection-change="handleSelectionChange"
      @sort-change="sortQuery"
      :default-sort = "{prop: 'pay_sn', order: 'descending'}"
      show-summary
      style="width: 100%">
      <el-table-column align="center" type="index" label="序号" width="65">
      </el-table-column>
      <el-table-column
        type="selection"
        prop="id"
        width="55">
      </el-table-column>
      <el-table-column label="支付单号" width="180">
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.pay_sn}}</span>
        </template>
      </el-table-column>
      <el-table-column label="子订单号" width="180">
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.order_sn}}</span>
        </template>
      </el-table-column>
      <el-table-column label="订单时间" width="180">
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.add_time}}</span>
        </template>
      </el-table-column>
      <el-table-column label="档口" width="180">
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.store_name}}</span>
        </template>
      </el-table-column>
      <el-table-column prop="goods_name" label="货品名称" width="200"></el-table-column>
      <el-table-column label="条码" width="180">
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.goods_serial}}</span>
        </template>
      </el-table-column>
      <el-table-column prop="goods_price" label="单价" width="100"></el-table-column>
      <el-table-column prop="goods_num" label="订单数量" width="100"></el-table-column>
      <el-table-column prop="goods_amount" label="货品金额" width="100"></el-table-column>
      <el-table-column prop="quehuo_number" label="缺货数量" width="100"></el-table-column>
      <el-table-column prop="jushou_number" label="拒收数量" width="100"></el-table-column>
      <el-table-column prop="shifa_number" label="实发数量" width="100"></el-table-column>
      <el-table-column prop="shifa_amount" label="实发金额" width="100"></el-table-column>
      <el-table-column label="配送计费方式" width="130">
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.shipping_charging_type | chargingTypeName}}</span>
        </template>
      </el-table-column>
      <el-table-column label="费率" width="100">
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.shipping_rate}}</span>
        </template>
      </el-table-column>
      <el-table-column prop="unpack_fee" label="拆包费" width="100"></el-table-column>
      <el-table-column prop="delivery_fee" label="配送费" width="100"></el-table-column>
      <el-table-column label="交款人" width="100">
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.jk_driver_name}}</span>
        </template>
      </el-table-column>
      <el-table-column label="司机计费方式" width="130">
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.driver_charging_type | chargingTypeName}}</span>
        </template>
      </el-table-column>
      <el-table-column label="费率" width="100">
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.driver_rate}}</span>
        </template>
      </el-table-column>
      <el-table-column prop="driver_fee" label="司机配送费" width="120"></el-table-column>
      <el-table-column label="录入状态" width="100">
          <template slot-scope="scope">
              <span class="table-col-text">{{scope.row.status | orderPaymentsStatus}}</span>
          </template>
      </el-table-column>
      <el-table-column label="记账人" width="100">
          <template slot-scope="scope">
              <span class="table-col-text">{{scope.row.jzr_name}}</span>
          </template>
      </el-table-column>
    </el-table>
    <div v-show="!listLoading" class="pagination-container">
      <el-pagination @size-change="handleSizeChange"
                     @current-change="handleCurrentChange" :current-page.sync="listQuery.page"
                     :page-sizes="[10,20,30, 50]" :page-size="listQuery.per_page"
                     layout="total, sizes, prev, pager, next, jumper" :total="total">
      </el-pagination>
    </div>
  </div>
</template>

<script>
  import { fetchList } from 'api/restfull';
  import { pickerOptions, showMsg, initDateMothRange, parseTime, param } from 'utils/index'
  import SelectStore from 'components/Selector/SelectStore';

  export default {
    name: 'exhcange_bottles',
    components: { SelectStore },
    data() {
      return {
        listLoading: false,
        list: null,
        total: null,
        baseURL: '/order_goods_payments',
        selectedRows: [],
        isSearchMore: true,
        listQuery: {
          page: 1,
          per_page: 20,
          status: '',
          add_time: undefined,
          store_id: '',
          pay_sn: undefined,
          jzr: '',
          jk_driver_id: ''
        },
        addTimePickerOption: {
          shortcuts: pickerOptions
        },
        statusMap: [
          { label: '全部', value: '' },
          { label: '未录入', value: 2 },
          { label: '已录入', value: 0 },
          { label: '已记账', value: 1 }
        ],
        adminOptions: [],
        storeOptions: [],
        driverOptions: []
      }
    },
    created() {
      this.getList();
      this.listQuery.add_time = initDateMothRange()
    },
    mounted() {
      this.getDriverOptions()
      this.getStoreOptions()
      this.getAdminOptions()
    },
    computed: {
    },
    methods: {
      getList() { // 列表
        this.listLoading = true;
        fetchList(this.listQuery, this.baseURL).then(response => {
          this.list = response.data.data;
          this.total = response.data.total;
          this.listLoading = false;
        })
      }, // 查询
      handleSearch() {
        this.getList();
      },
      handleReCalculate() {
        const query = this.getSelected();
        if (!query) {
          return false;
        }
        fetchList(query, '/main_order_payments/re_calculate').then(response => {
          showMsg(response.data)
        })
      },
      getDriverOptions() {
        fetchList({}, '/drivers_list').then(response => {
          this.driverOptions = response.data;
        })
      },
      getStoreOptions() {
        fetchList({}, '/stores').then(response => {
          this.storeOptions = response.data;
        })
      },
      getAdminOptions() {
        fetchList({}, '/admins_list').then(response => {
          this.adminOptions = response.data;
        })
      },
      getSelected() {
        let pay_ids = _.map(this.selectedRows, 'pay_id')
        if (_.isEmpty(pay_ids)) {
          this.$message({
            message: '请选择要操作的数据',
            type: 'error'
          });
          return false;
        }
        pay_ids = pay_ids.join(',')
        return { pay_ids };
      },
      handleSelectionChange(val) {
        this.selectedRows = val;
      },
      handleSearchMore() {
        return this.isSearchMore = !this.isSearchMore
      },
      sortQuery(sort) {
        this.listQuery.sort_by = sort.prop;
        this.listQuery.sort_order = sort.order;
        this.handleSearch();
      }, // 查看
      handlePrint() {
        const newWindow = window.open('_blank');   // 打开新窗口
        const codestr = document.getElementById('print-wrap').innerHTML;   // 获取需要生成pdf页面的div代码
        newWindow.document.write(codestr);   // 向文档写入HTML表达式或者JavaScript代码
        newWindow.document.close();     // 关闭document的输出流, 显示选定的数据
        newWindow.print();   // 打印当前窗口
        return true;
      },
      handleImport() {
        this.dialogUploadVisible = true;
      }, // 导出
      handleExport() {
        const params = this.listQuery
        if (params.add_time) {
          params.add_time_start = parseTime(params.add_time[0])
          params.add_time_end = parseTime(params.add_time[1])
          params.add_time = null
        }
        const query = param(params)
        window.location.href = '/export/order_goods_payments?' + query;
      }, // 数据保存
      handleSizeChange(val) {
        this.listQuery.per_page = val;
        this.handleSearch();
      },
      handleCurrentChange(val) {
        this.listQuery.page = val;
        this.handleSearch();
      },
      queryChangeStore(val) {
        this.listQuery.store_id = val
      }
    }
  }
</script>
<style media="scss">
  .el-table .info-row {
    background: #c9e5f5;
  }
  .el-table .positive-row {
    background: #e2f0e4;
  }
  .el-button-group .el-button:not(:last-child){
    margin-right: 20px
  }
</style>
