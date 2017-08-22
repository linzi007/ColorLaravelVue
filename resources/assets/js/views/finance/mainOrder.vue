<template>
  <div class="app-container calendar-list-container">
    <div class="filter-container">
      <el-row>
          <el-button-group>
            <el-button class="filter-item" type="text"
              @click="handleRecord()">收款登记</el-button>
            <el-button class="filter-item" type="text"
              @click="handleJizhang()">记账</el-button>
            <el-button class="filter-item" type="text"
              @click="handleFanjizhang()">反记账</el-button>
            <el-button class="filter-item" type="text"
              @click="handleReCalculate()">重算配送费</el-button>
            <el-button class="filter-item" type="text"
              @click="this.dialogSearchVisible=true">高级查询</el-button>
            <router-link to="/finance/subOrder">切换为子订单模式</router-link>
            <span>|</span>
            <el-button class="filter-item" type="text"
              @click="handleExport()"
            >导出</el-button>
            <el-button class="filter-item" type="text"
              @click="handlePrint()"
            >打印</el-button>
          </el-button-group>
      </el-row>
      <el-row>
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
      </el-row>
    </div>
    <el-table
        :data="list" ref="mainOrderTable" v-loading.body="listLoading"
        boder fit highlight-current-row
        @row-dbclick="handleEdit"
        @sort-change="sortQuery"
        :default-sort = "{prop: 'pay_sn', order: 'descending'}"
        show-summary
        @selection-change="handleSelectionChange"
        style="width: 100%">
        <el-table-column align="center" type="index" label="序号" width="65">
        </el-table-column>
        <el-table-column
          type="selection"
          prop="pay_id"
          width="55">
        </el-table-column>
        <el-table-column  min-width="100px" label="支付单号">
          <template scope="scope">
            <span class="link-type" @click="handleEdit(scope.row)">{{scope.row.pay_sn}}</span>
          </template>
        </el-table-column>
    </el-table>

    <div v-show="!listLoading" class="pagination-container">
      <el-pagination @size-change="handleSizeChange"
      @current-change="handleCurrentChange" :current-page.sync="listQuery.current_page"
        :page-sizes="[10,20,30, 50]" :page-size="listQuery.per_page"
        layout="total, sizes, prev, pager, next, jumper" :total="total">
      </el-pagination>
    </div>

    <el-dialog title="收款登记" size="large" :visible.sync="dialogFormVisible">
      <mainOrderDetail :mainOrder="formData" :inputStaus="dialogFormStauts"></mainOrderDetail>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">取 消</el-button>
        <el-button type="primary" @click="dialogFormVisible = false">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
  import { fetchList, fetchUpdate } from 'api/restfull';
  import mainOrderDetail from './_mainOrderDetail.vue'
  export default {
    name: 'mainOrder',
    components: { mainOrderDetail },
    data() {
      return {
        listLoading: false,
        list: null,
        total: null,
        sortBy: 'pay_sn',
        sortOrder: 'descending',
        baseURL: '/main_order_payments',
        selectedRows: [],
        listQuery: {
          current_page: 1,
          per_page: 20,
          add_time: undefined,
          pay_sn: undefined,
          status: undefined,
          jkr_jd: undefined,
          jzr_id: undefined
        },
        dialogFormVisible: false,
        dialogFormStauts: '',
        dialogSearchVisible: false,
        dialogGoodsListVisible: false,
        tableKey: 0,
        formData: {},
        validateRules: {},
        addTimePickerOption: {
          shortcuts: [{
            text: '最近一周',
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
              picker.$emit('pick', [start, end]);
            }
          }, {
            text: '最近一个月',
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
              picker.$emit('pick', [start, end]);
            }
          }, {
            text: '最近三个月',
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
              picker.$emit('pick', [start, end]);
            }
          }]
        }
      }
    },
    created() {
      this.getList();
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
      }, // 详情
      getDetail(pay_id) {
        fetchList(this.listQuery, this.baseURL + '/' + pay_id).then(response => {
          this.formData = response.data;
          this.formData.goods_list.map(goods => {
            goods.edit = true;
            return goods;
          })
        })
      }, // checked
      getSelected() {
        return '';
      }, // 查询
      handleSearch() {
        this.getList();
      }, // 排序查询
      sortQuery(sort) {
        this.sortBy = sort.column;
        this.sortOrder = sort.order;
        this.handleSearch();
      }, // 查看
      handleShow(row) {
        this.getDetail(row.pay_id);
        this.dialogFormStauts = 'view';
        this.dialogFormVisible = true;
      },
      handleSelectionChange(val) {
        this.selectedRows = val;
      }, // 编辑
      handleEdit(row) {
        this.getDetail(row.pay_id);
        this.dialogFormStauts = 'edit';
        this.dialogFormVisible = true;
      }, // 记账
      handleJizhang() {
        this.getSelected();
      }, // 反记账
      handleFanjizhang() {
        this.getSelected();
      },
      handleReCalculate() {
        console.log('计算中');
        this.getSelected();
      },
      handlePrint() {
        return true;
      }, // 导出
      handleExport() {
        fetchList(this.listQuery, '/export/main_order_payments').then(response => {
          if (response.data.status) {
            this.$message({
              message: response.data.message,
              type: 'error'
            });
            return false;
          }
          this.$message({
            message: response.data.message,
            type: 'success'
          });
          return false;
        })
      }, // 数据保存
      handleSave() {
        fetchUpdate(this.formData, this.baseURL + '/' + formData.pay_id).then(response => {
          if (!response.data.status) {
            this.$message({
              message: response.data.message,
              type: 'error'
            });
            return false;
          }
          this.$notify({
            title: '更新成功',
            message: '更新成功',
            type: 'success',
            duration: 1000
          })
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
        this.listQuery.current_page = val;
        this.handleSearch();
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
  }
</script>
