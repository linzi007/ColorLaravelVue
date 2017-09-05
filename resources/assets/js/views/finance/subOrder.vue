<template>
  <div class="app-container calendar-list-container">
    <div class="filter-container">
      <el-row :gutter="10" type="flex" justify="left">
        <el-button-group>
          <el-button class="top-button-group" type="text"
                     @click="handleReCalculate()">重算配送费</el-button>
          <router-link to="/finance/main-order" class="el-button filter-item el-button--text">切换为主订单模式 | </router-link>
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
        <span class="demonstration">子订单号</span>
        <el-input placeholder="子订单号" v-model="listQuery.order_sn" style="width:200px"></el-input>
        <el-button class="filter-item" type="primary" icon="search"
                   @click="handleSearch()">查询</el-button>
        <el-button class="filter-item" type="text"
                   @click="handleSearchMore()">更多</el-button>
      </el-row>
      <div :class="{ 'search-more':isSearchMore }">
        <el-row>
          <span class="demonstration">支付单号</span>
          <el-input placeholder="支付单号" v-model="listQuery.pay_sn" style="width:200px"></el-input>
          <span class="demonstration">录入状态</span>
          <el-select v-model="listQuery.status">
            <el-option
                    v-for="item in statusMap"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value">
            </el-option>
          </el-select>
          <span class="demonstration">交款人</span>
          <select-driver :selected="listQuery.jkr_jd" @changeSelect="queryChangeJkr"></select-driver>
        </el-row>
      </div>
    </div>
    <el-table id="print-wrap"
              :data="list" ref="mainOrderTable" v-loading.body="listLoading"
              border fit highlight-current-row
              @sort-change="sortQuery"
              :default-sort = "{prop: 'pay_sn', order: 'descending'}"
              show-summary
              @selection-change="handleSelectionChange"
              :row-class-name="tableRowClassName"
              style="width: 100%">
      <el-table-column align="center" type="index" label="序号" width="65">
      </el-table-column>
      <el-table-column
              type="selection"
              prop="pay_id"
              width="55">
      </el-table-column>
      <el-table-column label="支付单号" width="200" sortable>
        <template scope="scope">
          <span class="table-col-text">{{scope.row.pay_sn}}</span>
        </template>
      </el-table-column>
      <el-table-column label="子订单号" width="200">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.pay_sn}}</span>
        </template>
      </el-table-column>
      <el-table-column label="订单时间" width="180">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.add_time}}</span>
        </template>
      </el-table-column>
      <el-table-column label="档口" width="150">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.store_id}}</span>
        </template>
      </el-table-column>
      <el-table-column prop="goods_amount" label="货品金额" width="100"></el-table-column>
      <el-table-column prop="quehuo" label="缺货金额" width="100"></el-table-column>
      <el-table-column prop="jushou" label="拒收金额" width="100"></el-table-column>
      <el-table-column prop="shifa" label="实发金额" width="100"></el-table-column>
      <el-table-column prop="qiandan" label="签单金额" width="100"></el-table-column>
      <el-table-column prop="ziti" label="自提金额" width="100"></el-table-column>
      <el-table-column prop="qita" label="其他金额" width="100"></el-table-column>
      <el-table-column prop="weicha" label="尾差金额" width="100"></el-table-column>
      <el-table-column label="扣减备注" width="100">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.desc_remark}}</span>
        </template>
      </el-table-column>
      <el-table-column prop="promotion_amount" label="代金券" width="100"></el-table-column>
      <el-table-column prop="yingshou" label="应收金额" width="100"></el-table-column>
      <el-table-column prop="pd_amount" label="预存款" width="100"></el-table-column>
      <el-table-column prop="pos" label="POS刷卡" width="100"></el-table-column>
      <el-table-column prop="weixin" label="微信" width="100"></el-table-column>
      <el-table-column prop="alipay" label="支付宝" width="100"></el-table-column>
      <el-table-column prop="yizhifu" label="翼支付" width="100"></el-table-column>
      <el-table-column prop="cash" label="现金" width="100"></el-table-column>
      <el-table-column prop="shishou" label="实收金额" width="100"></el-table-column>
      <el-table-column label="交款日期" width="180">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.jk_at}}</span>
        </template>
      </el-table-column>
      <el-table-column label="刷卡单号" width="200">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.out_pay_sn}}</span>
        </template>
      </el-table-column>
      <el-table-column label="交款人" width="100">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.jkr}}</span>
        </template>
      </el-table-column>
      <el-table-column prop="delivery_fee" label="配送费" width="100"></el-table-column>
      <el-table-column label="收款备注" width="200">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.remark}}</span>
        </template>
      </el-table-column>
      <el-table-column label="录入状态" width="100">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.status | orderPaymentsStatus}}</span>
        </template>
      </el-table-column>
      <el-table-column label="记账人" width="100">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.jzr}}</span>
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
  import { fetchList, fetchCreate } from 'api/restfull';
  import mainOrderDetail from './_mainOrderDetail.vue'
  import { Loading } from 'element-ui';
  import { pickerOptions, showMsg, initDateMothRange } from 'utils/index'
  import SelectDriver from 'components/Selector/SelectDriver';

  export default {
    name: 'mainOrder',
    components: { mainOrderDetail, SelectDriver },
    data() {
      return {
        listLoading: false,
        isSearchMore: true,
        list: null,
        total: null,
        sortBy: 'pay_sn',
        sortOrder: 'descending',
        baseURL: '/sub_order_payments',
        selectedRows: [],
        listQuery: {
          page: 1,
          per_page: 20,
          add_time: undefined,
          pay_sn: undefined,
          order_sn: undefined,
          status: undefined,
          jkr_jd: undefined,
          jzr_id: undefined
        },
        statusMap: [
          { label: '未录入', value: 2 },
          { label: '已录入', value: 0 },
          { label: '已记账', value: 1 }
        ],
        dialogFormVisible: false,
        dialogFormStauts: '',
        dialogSearchVisible: false,
        dialogGoodsListVisible: false,
        tableKey: 0,
        formData: {},
        validateRules: {},
        addTimePickerOption: {
          shortcuts: pickerOptions
        }
      }
    },
    created() {
      this.getList();
      this.listQuery.add_time = initDateMothRange()
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
      }, // checked
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
      }, // 查询
      handleSearch() {
        this.getList();
      },
      handleSearchMore() {
        return this.isSearchMore = !this.isSearchMore
      }, // 排序查询
      sortQuery(sort) {
        this.sortBy = sort.column;
        this.sortOrder = sort.order;
        this.handleSearch();
      }, // 查看
      showError(msg) {
        this.$message({
          message: msg,
          type: 'error'
        });
        return false;
      },
      handleSelectionChange(val) {
        this.selectedRows = val;
      }, // 编辑
      handleReCalculate() {
        const query = this.getSelected();
        if (!query) {
          return false;
        }
        fetchList(query, '/main_order_payments/re_calculate').then(response => {
          showMsg(response.data)
        })
      },
      handlePrint() {
        const newWindow = window.open('_blank');   // 打开新窗口
        const codestr = document.getElementById('print-wrap').innerHTML;   // 获取需要生成pdf页面的div代码
        newWindow.document.write(codestr);   // 向文档写入HTML表达式或者JavaScript代码
        newWindow.document.close();     // 关闭document的输出流, 显示选定的数据
        newWindow.print();   // 打印当前窗口
        return true;
      }, // 导出
      handleExport() {
        fetchList(this.listQuery, '/export/sub_order_payments').then(response => {
          showMsg(response.data)
        })
      }, // 数据保存
      handleSizeChange(val) {
        this.listQuery.per_page = val;
        this.handleSearch();
      },
      handleCurrentChange(val) {
        this.listQuery.page = val;
        this.handleSearch();
      },
      queryChangeJkr(val) {
        this.listQuery.jkr_id = val
      },
      tableRowClassName(row, index) {
        if (row.status === 1) {
          return 'info-row';
        }
        if (row.status === 0) {
          return 'positive-row';
        }
      }
    }
  }
</script>
<style media="scss">
  .el-dialog__body{
    font-size: 16px
  }
  .search-more{
    display: none
  }
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
