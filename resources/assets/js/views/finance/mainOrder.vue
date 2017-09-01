<template>
  <div class="app-container calendar-list-container">
    <div class="filter-container">
      <el-row :gutter="10" type="flex" justify="left">
        <el-button-group>
          <el-button class="top-button-group" type="text"
            @click="handleRecord()">收款登记</el-button>
          <el-button class="top-button-group" type="text"
            @click="handleJizhang()">记账</el-button>
          <el-button class="top-button-group" type="text"
            @click="handleFanjizhang()">反记账</el-button>
          <el-button class="top-button-group" type="text"
            @click="handleReCalculate()">重算配送费</el-button>
          <router-link to="/finance/subOrder" class="el-button filter-item el-button--text">切换为子订单模式</router-link>
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
        <span class="demonstration">支付单号</span>
        <el-input placeholder="支付单号" v-model="listQuery.pay_sn" style="width:100px"></el-input>
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
          <select-driver :selected="listQuery.jk_driver_jd" @changeSelect="queryChangeJkr"></select-driver>
        </el-row>
      </div>
    </div>
    <el-table id="print-wrap" class="myDivToPrint"
        :data="list" ref="mainOrderTable" v-loading.body="listLoading"
        boder fit highlight-current-row
        @row-dbclick="handleEdit"
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
        <el-table-column label="支付单号" width="200">
          <template scope="scope">
            <span class="link-type" @click="handleEdit(scope.row)">{{scope.row.pay_sn}}</span>
          </template>
        </el-table-column>
        <el-table-column label="订单时间" width="180">
          <template scope="scope">
            <span class="table-col-text">{{scope.row.add_time}}</span>
          </template>
        </el-table-column>
        <el-table-column label="货品金额" width="100">
          <template scope="scope">
            <span class="table-col-text">{{scope.row.goods_amount}}</span>
          </template>
        </el-table-column>
        <el-table-column label="缺货金额" width="100">
          <template scope="scope">
            <span class="table-col-text">{{scope.row.quehuo}}</span>
          </template>
        </el-table-column>
        <el-table-column label="拒收金额" width="100">
          <template scope="scope">
            <span class="table-col-text">{{scope.row.jushou}}</span>
          </template>
        </el-table-column>
        <el-table-column label="实发金额" width="100">
          <template scope="scope">
            <span class="table-col-text">{{scope.row.shifa}}</span>
          </template>
        </el-table-column>
        <el-table-column label="签单金额" width="100">
          <template scope="scope">
            <span class="table-col-text">{{scope.row.qiandan}}</span>
          </template>
        </el-table-column>
        <el-table-column label="自提金额" width="100">
          <template scope="scope">
            <span class="table-col-text">{{scope.row.ziti}}</span>
          </template>
        </el-table-column>
        <el-table-column label="其他金额" width="100">
          <template scope="scope">
            <span class="table-col-text">{{scope.row.qita}}</span>
          </template>
        </el-table-column>
        <el-table-column label="尾差金额" width="100">
          <template scope="scope">
            <span class="table-col-text">{{scope.row.weicha}}</span>
          </template>
        </el-table-column>
        <el-table-column label="扣减备注" width="100">
          <template scope="scope">
            <span class="table-col-text">{{scope.row.desc_remark}}</span>
          </template>
        </el-table-column>
        <el-table-column label="代金券" width="100">
          <template scope="scope">
            <span class="table-col-text">{{scope.row.promotion_amount}}</span>
          </template>
        </el-table-column>
        <el-table-column label="应收金额" width="100">
          <template scope="scope">
            <span class="table-col-text">{{scope.row.yingshou}}</span>
          </template>
        </el-table-column>
        <el-table-column label="预存款" width="100">
            <template scope="scope">
                <span class="table-col-text">{{scope.row.pd_amount}}</span>
            </template>
        </el-table-column>
        <el-table-column label="POS刷卡" width="100">
            <template scope="scope">
                <span class="table-col-text">{{scope.row.pos}}</span>
            </template>
        </el-table-column>
        <el-table-column label="微信" width="100">
            <template scope="scope">
                <span class="table-col-text">{{scope.row.weixin}}</span>
            </template>
        </el-table-column>
        <el-table-column label="支付宝" width="100">
            <template scope="scope">
                <span class="table-col-text">{{scope.row.alipay}}</span>
            </template>
        </el-table-column>
        <el-table-column label="翼支付" width="100">
            <template scope="scope">
                <span class="table-col-text">{{scope.row.yizhifu}}</span>
            </template>
        </el-table-column>
        <el-table-column label="现金" width="100">
            <template scope="scope">
                <span class="table-col-text">{{scope.row.cash}}</span>
            </template>
        </el-table-column>
        <el-table-column label="实收金额" width="100">
            <template scope="scope">
                <span class="table-col-text">{{scope.row.promotion_amount}}</span>
            </template>
        </el-table-column>
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
                <span class="table-col-text">{{scope.row.jk_driver_id}}</span>
            </template>
        </el-table-column>
        <el-table-column label="配送费" width="100">
            <template scope="scope">
                <span class="table-col-text">{{scope.row.delivery_fee}}</span>
            </template>
        </el-table-column>
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
        <el-table-column label="录入状态" width="100">
          <template scope="scope">
            <span class="table-col-text">{{scope.row.status | orderPaymentsStatus}}</span>
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

    <el-dialog title="收款登记" size="large" class="dialog" ref="mainOrderDialog" :visible.sync="dialogFormVisible">
      <mainOrderDetail :mainOrder="formData" :inputStaus="dialogFormStauts" @formSave="handleSave" @handleCancel="handleCancel"></mainOrderDetail>
    </el-dialog>
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
        baseURL: '/main_order_payments',
        selectedRows: [],
        listQuery: {
          page: 1,
          per_page: 20,
          sort_by: 'pay_sn',
          sort_order: 'descending',
          add_time: undefined,
          pay_sn: undefined,
          status: undefined,
          jk_driver_jd: undefined,
          jzr_id: undefined
        },
        statusMap: [
          { label: '全部', value: '' },
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
    computed: {},
    methods: {
      getList() { // 列表
        this.listLoading = true;
        fetchList(this.listQuery, this.baseURL).then(response => {
          this.list = response.data.data;
          this.total = response.data.total;
          this.listLoading = false;
        })
      }, // 详情
      getDetail(pay_id) {
        const loadingInstance = Loading.service();
        fetchList(this.listQuery, this.baseURL + '/' + pay_id).then(response => {
          this.dialogFormVisible = true;
          loadingInstance.close();
          this.formData = response.data;
          if (this.formData.ck_at) {
            this.formData.ck_at = new Date(this.formData.ck_at)
          }
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
        this.listQuery.sort_by = sort.column;
        this.listQuery.sort_order = sort.order;
        this.handleSearch();
      }, // 查看
      handleShow(row) {
        this.getDetail(row.pay_id);
        this.dialogFormStauts = 'view';
      },
      handleRecord() {
        console.log(this.selectedRows);
        if (this.selectedRows.length === 0) {
          this.showError('请选择要登记的数据')
          return false
        }
        this.handleEdit(this.selectedRows[0])
      },
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
      handleEdit(row) {
        this.getDetail(row.pay_id);
        this.dialogFormStauts = 'edit';
      }, // 记账
      handleJizhang() {
        const query = this.getSelected();
        if (!query) {
          return false;
        }
        fetchList(query, this.baseURL + '/jizhang').then(response => {
          showMsg(response.data)
        })
      }, // 反记账
      handleFanjizhang() {
        const query = this.getSelected();
        if (!query) {
          return false;
        }
        fetchList(query, this.baseURL + '/fanjizhang').then(response => {
          showMsg(response.data)
        })
      },
      handleReCalculate() {
        const query = this.getSelected();
        if (!query) {
          return false;
        }
        fetchList(query, this.baseURL + '/re_calculate').then(response => {
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
        fetchList(this.listQuery, '/export/main_order_payments').then(response => {
          showMsg(response.data)
        })
      }, // 数据保存
      handleSave() {
        this.$refs.orderMainForm.validate(valid => {
          if (valid) {
            fetchCreate(this.formData, this.baseURL).then(response => {
              if (!response.data.status) {
                this.showError(response.data.msg)
              }
              this.$notify({
                title: '更新成功',
                message: '更新成功',
                type: 'success',
                duration: 1000
              })
            })
          } else {
            return false;
          }
        })
      }, // 取消
      handleCancel() {
        this.dialogFormVisible = false;
      }, // 保存并关闭
      handleSaveAndCancel() {
        this.handleSave();
        this.handleCancel();
      },
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
      tableRowClassName(row) {
        if (row.status === 1) {
          return 'info-row';
        }
        if (row.status === 0) {
          return 'positive-row';
        }
      },
      initFormData() {
        this.formData = {
          id: 0,
          pay_id: '',
          pay_sn: '',
          store_id: 0,
          add_time: 0,
          quehuo: 0,
          jushou: 0,
          shifa: 0,
          qiandan: 0,
          ziti: 0,
          qita: 0,
          weicha: 0,
          desc_remark: '备注',
          yingshou: 0,
          pos: 0,
          weixin: 0,
          alipay: 0,
          yizhifu: 0,
          cash: 0,
          shishou: 0,
          delivery_fee: 0,
          driver_fee: 0,
          driver_id: 0,
          second_driver_id: 0,
          jk_driver_id: 0,
          status: 0,
          jlr: 0,
          jzr: 0,
          jz_at: 0,
          created_at: 0,
          updated_at: 0,
          main_order_id: 0,
          order_from: 4,
          store_name: '',
          is_own_shop: 1,
          buyer_id: '0',
          buyer_name: '小多',
          goods_amount: 0,
          goods_count: 0,
          sku_count: 0,
          order_amount: 0,
          pay_amount: 0,
          order_state: 40,
          receiver_address_id: 0,
          receiver_shop_id: 0,
          receiver_shop_name: '',
          receiver_area_info: '福建省厦门市湖里区',
          receiver_address_detail: 'xxxx',
          receiver_name: '小彩',
          receiver_phone: '1526047xxxx',
          order_message: '',
          goods_list: []
        }
      }
    }
  };
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
  @media print {
    .myDivToPrint {
      background-color: white;
      height: 100%;
      width: 100%;
      position: fixed;
      top: 0;
      left: 0;
      margin: 0;
      padding: 15px;
      font-size: 14px;
      line-height: 18px;
    }
  }
</style>
