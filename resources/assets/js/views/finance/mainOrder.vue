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
      <el-form :model="formData" ref="orderMainForm">
        <el-row>
            <span>订单信息</span>
        </el-row>
        <el-row>
            <el-col :span="8">
              <el-form-item label="支付单号">
                <span class="form-item">
                  {{formData.pay_sn}}
                </span>
              </el-form-item>
            </el-col>
            <el-col :span="8">
              <el-form-item label="订单时间">
                <span class="form-item">
                  {{formData.add_time}}
                </span>
              </el-form-item>
            </el-col>
            <el-col :span="8">
              <el-form-item label="门店名称">
                <span class="form-item">
                  {{formData.receiver_shop_name}}
                </span>
              </el-form-item>
            </el-col>
        </el-row>
        <el-row>
            <el-col :span="8">
              <el-form-item label="收货人">
                <span class="form-item">
                  {{formData.receiver_name}}
                </span>
              </el-form-item>
            </el-col>
            <el-col :span="16">
              <el-form-item label="详细地址">
                <span class="form-item">
                  {{formData.receiver_area_info}} {{formData.receiver_address_detail}}
                </span>
              </el-form-item>
            </el-col>
        </el-row>

        <el-row>
          <span class="form-line">实发金额{{formData.shifa}}</span><span>（备注实发=货品金额-缺货-拒收）</span>
        </el-row>
        <el-row>
            <el-col :span="8">
              <el-form-item label="货品金额">
                <span class="form-item">
                  {{formData.receiver_name}}
                </span>
              </el-form-item>
            </el-col>
            <el-col :span="6">
              <el-form-item label="缺货金额">
                <span class="form-item">
                  {{formData.quehuo}}
                </span>
              </el-form-item>
            </el-col>
            <el-col :span="6">
              <el-form-item label="拒收金额">
                <span class="form-item">
                  {{formData.jushou}}
                </span>
              </el-form-item>
            </el-col>
            <el-col :span="4">
                <el-button @click="toogleDialogGoodsList()">缺货/拒收录入</el-button>
            </el-col>
        </el-row>
        <el-row>
            <el-table v-show="dialogGoodsListVisible"
                ref='orderGoods'
                :data="formData.goods_list"
                style="width: 100%">
                <el-table-column width="210px" align="center" label="货品名称">
                  <template scope="scope">
                    <span>{{scope.row.goods_name}}</span>
                  </template>
                </el-table-column>
                <el-table-column width="110px" align="center" label="条码">
                  <template scope="scope">
                    <span>{{scope.row.goods_serial}}</span>
                  </template>
                </el-table-column>
                <el-table-column width="150px" align="center" label="档口名称">
                  <template scope="scope">
                    <span>{{scope.row.store_id}}</span>
                  </template>
                </el-table-column>
                <el-table-column width="150px" align="center" label="单价">
                  <template scope="scope">
                    <span>{{scope.row.goods_price}}</span>
                  </template>
                </el-table-column>
                <el-table-column width="110px" align="center" label="数量">
                  <template scope="scope">
                    <span>{{scope.row.goods_num}}</span>
                  </template>
                </el-table-column>
                <el-table-column width="110px" align="center" label="缺货数量">
                  <template scope="scope">
                    <el-input v-show="scope.row.edit" size="small" v-model="scope.row.payments.quehuo_number"></el-input>
                    <span v-show="!scope.row.edit">{{ scope.row.payments.quehuo_number }}</span>
                  </template>
                </el-table-column>
                <el-table-column width="110px" fix="right" align="center" label="拒收数量">
                  <template scope="scope">
                    <el-input v-show="scope.row.edit" size="small" v-model="scope.row.payments.jushou_number"></el-input>
                    <span v-show="!scope.row.edit">{{ scope.row.payments.jushou_number }}</span>
                  </template>
                </el-table-column>
                <el-table-column width="110px" fix="right" align="center" label="实发数量">
                  <template scope="scope">
                    <span>{{scope.row.goods_num - scope.row.payments.quehuo_number - scope.row.payments.jushou_number}}</span>
                  </template>
                </el-table-column>
                <el-table-column align="center" fix="right" label="编辑" width="120">
                  <template scope="scope">
                    <el-button v-show='!scope.row.edit' type="primary" @click='scope.row.edit=true' size="small" icon="edit">编辑</el-button>
                    <el-button v-show='scope.row.edit' type="success" @click='scope.row.edit=false' size="small" icon="check">完成</el-button>
                  </template>
                </el-table-column>
            </el-table>
        </el-row>

        <el-row>
          <span class="form-line">应收金额{{formData.yingshou}}</span><span>（备注：应收=实发金额-签单-自提-其他-尾差）</span>
        </el-row>
        <el-row>
            <el-col :span="8">

            </el-col>
        </el-row>

        <el-row>
          <span class="form-line">实收金额{{formData.shishou}}</span><span>（备注：实收=预存款+POS+微信+支付宝+现金）</span>
        </el-row>
        <el-row>

        </el-row>
        <el-row>
          <span class="form-line">配送费{{formData.shishou}}</span>
        </el-row>
        <el-row>
          <el-col :span="12">
            <el-form-item label="货物配送费">
              <el-input placeholder="货物配送费" v-model="formData.shipping_fee">
                <el-button slot="append" @click="handleReCalculate()">重算</el-button>
              </el-input>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="司机配送费">
              <el-input placeholder="司机配送费" v-model="formData.shipping_fee">
                <el-button slot="append" @click="handleReCalculate()">重算</el-button>
              </el-input>
            </el-form-item>
          </el-col>
          <el-row>
              <el-col :span="6">
                  <el-checkbox v-model="formData.sencod_driver_id" :checked="formData.sencod_driver_id">二次配送</el-checkbox>
              </el-col>
              <el-col :span="18">
                <div>
                  <span>首次配送司机</span>
                  <el-select
                    v-model="formData.driver_id"
                    filterable
                    placeholder="请选择">
                    <el-option
                      v-for="item in driverOptions"
                      :key="item.id"
                      :label="item.name"
                      :value="item.id">
                    </el-option>
                  </el-select>
                </div>
              </el-col>
          </el-row>
          <el-row>
            <el-col :span="6">
                <el-checkbox checked>换盖</el-checkbox>
            </el-col>
            <el-col :span="6">
              <el-form-item label="档口">
                <el-select size="small"
                  filterable
                  placeholder="请选择">
                  <el-option
                    v-model="exchangeBottle.store_id"
                    v-for="item in storeOptions"
                    :key="item.store_id"
                    :label="item.store_name"
                    :value="item.store_id">
                  </el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-input placeholder="换盖金额" v-model="exchangeBottle.amount">
                <el-button slot="append" @click="handleExchangeBottle()">保存</el-button>
              </el-input>
            </el-col>
          </el-row>
        </el-row>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false">取 消</el-button>
        <el-button type="primary" @click="dialogFormVisible = false">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
  import { fetchList, fetchUpdate } from 'api/restfull';
  export default {
    name: 'mainOrder',
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
        exchangeBottle: {}, // 换盖金额
        validateRules: {},
        driverOptions: [],
        storeOptions: [],
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
      }, // 换瓶盖数据保存
      handleExchangeBottle() {
        fetchUpdate(exchangeBottle, '/exchange_bottle').then(response => {
          let msgType = 'error'
          if (response.data.status) {
            msgType = 'success'
          }
          this.$message({
            message: response.data.message,
            type: msgType
          });
        })
      }, // 保存
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
      toogleDialogGoodsList() {
        this.dialogGoodsListVisible = !this.dialogGoodsListVisible;
      }, // 编辑订单货品信息
      saveDialogGoodsList(row) {
        fetchUpdate(row, '/order_goods_payments').then(response => {
          let msgType = 'error'
          if (response.data.status) {
            msgType = 'success'
          }
          this.$message({
            message: response.data.message,
            type: msgType
          });
        })
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
