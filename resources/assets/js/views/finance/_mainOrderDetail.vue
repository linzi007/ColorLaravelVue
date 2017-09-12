<template>
  <el-form :model="orderPayments" :rules="rules" label-width="100px" ref="orderMainForm">
    <el-row :gutter="30" type="flex" justify="left">
      <el-col :span="6">
        <el-button-group>
          <el-button type="primary" size="small" @click="handlePreOne()" icon="arrow-left">上一条</el-button>
          <el-button type="primary" size="small" @click="handleNextOne()">下一条<i class="el-icon-arrow-right el-icon--right"></i></el-button>
        </el-button-group>
      </el-col>
    </el-row>
    <el-row>
        <h3 class="form-title">订单信息</h3>
    </el-row>
    <el-row>
      <div class="form-line-head">
        订单信息
      </div>
    </el-row>
    <el-row :gutter="20">
        <el-col :span="8">
          <el-form-item label="支付单号：">
            <span class="form-item">
              {{orderPayments.pay_sn}}
            </span>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="订单时间：">
            <span class="form-item">
              {{orderPayments.add_time}}
            </span>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="门店名称：">
            <span class="form-item">
              {{orderPayments.receiver_shop_name}}
            </span>
          </el-form-item>
        </el-col>
    </el-row>
    <el-row :gutter="20">
        <el-col :span="8">
          <el-form-item label="收货人：">
            <span class="form-item">
              {{orderPayments.receiver_name}}
            </span>
          </el-form-item>
        </el-col>
        <el-col :span="16">
          <el-form-item label="详细地址：">
            <span class="form-item">
              {{orderPayments.receiver_area_info}} {{orderPayments.receiver_address_detail}}
            </span>
          </el-form-item>
        </el-col>
    </el-row>
    <el-row>
      <div class="form-line-head" style="background: #D3DCE6">
        实发金额：<span class="form-head-price">{{shifaAmount | currency}}</span><span class="form-line-head-desc">（备注实发=货品金额-缺货-拒收）</span>
      </div>
    </el-row>
    <el-row :gutter="20">
        <el-col :span="8">
          <el-form-item label="货品金额：">
            <span class="form-item">
              {{orderPayments.goods_amount | currency}}
            </span>
          </el-form-item>
        </el-col>
        <el-col :span="6">
          <el-form-item label="缺货金额：">
            <span class="form-item">
              {{orderPayments.quehuo | currency}}
            </span>
          </el-form-item>
        </el-col>
        <el-col :span="6">
          <el-form-item label="拒收金额：">
            <span class="form-item">
              {{orderPayments.jushou | currency}}
            </span>
          </el-form-item>
        </el-col>
        <el-col :span="4">
            <el-button size="small" type="success" @click="toogleGoodsList()">缺货/拒收录入</el-button>
        </el-col>
    </el-row>
    <el-row>
      <order-goods-list :goodsList="orderPayments.goods_list" @goods-change="handleGoodsListChange"
        v-show="goodsListVisible">
      </order-goods-list>
    </el-row>

    <el-row>
      <div class="form-line-head" style="background: #F9FAFC">
        应收金额：<span class="form-head-price">{{yingshouAmount | currency}}</span><span class="form-line-head-desc">（备注：应收=实发金额-签单-自提-其他-尾差）</span>
      </div>
    </el-row>
    <el-row :gutter="20">
        <el-col :span="8">
          <el-form-item label="签单金额：" prop="qiandan">
            <el-input placeholder="签单金额" v-model.number="orderPayments.qiandan">
            </el-input>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="自提金额：" prop="ziti">
            <el-input placeholder="自提金额" v-model.number="orderPayments.ziti">
            </el-input>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="尾差金额：" prop="weicha">
            <el-input placeholder="尾差金额" v-model.number="orderPayments.weicha">
            </el-input>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="其他金额：" prop="qita">
            <el-input placeholder="其他金额" v-model.number="orderPayments.qita">
            </el-input>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="备注：" prop="desc_remark">
            <el-input type="textarea" :autosize="{ minRows: 1, maxRows: 4}"
              placeholder="扣减说明" v-model="orderPayments.desc_remark">
            </el-input>
          </el-form-item>
        </el-col>
        <el-col :span="8">
          <el-form-item label="代金券：">
            <span class="form-item">
              {{orderPayments.promotion_amount}}
            </span>
          </el-form-item>
        </el-col>
    </el-row>

    <el-row>
      <div class="form-line-head" style="background: #8492A6">
        实收金额：<span class="form-head-price">{{shishouAmount | currency}}</span><span class="form-line-head-desc">（备注：实收=预存款+POS+微信+支付宝+现金）</span>
      </div>
    </el-row>
    <el-row :gutter="20">
      <el-col :span="8">
        <el-form-item label="预存款金额：">
          <span class="form-item">
            {{orderPayments.pd_amount}}
          </span>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="POS金额：" prop="pos">
          <el-input placeholder="POS金额" v-model.number="orderPayments.pos">
          </el-input>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="刷卡单号：" prop="out_pay_sn">
          <el-input placeholder="刷卡单号" v-model="orderPayments.out_pay_sn">
          </el-input>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="微信金额：" prop="weixin">
          <el-input placeholder="微信金额" v-model.number="orderPayments.weixin">
          </el-input>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="支付宝金额：" prop="alipay">
          <el-input placeholder="支付宝金额" v-model.number="orderPayments.alipay">
          </el-input>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="现金：" prop="cash">
          <el-input placeholder="现金" v-model.number="orderPayments.cash">
          </el-input>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="翼支付金额：" prop="yizhifu">
          <el-input placeholder="翼支付金额" v-model.number="orderPayments.yizhifu">
          </el-input>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="交款人：" prop="jk_driver_id">
          <el-select v-model="orderPayments.jk_driver_id" filterable placeholder="请输入关键词">
            <el-option
                    v-for="item in driverOptions"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id">
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="收款日期：" prop="jk_at">
          <el-date-picker
            v-model="orderPayments.jk_at"
            type="datetime"
            placeholder="选择日期时间"
            align="right"
            :picker-options="dateOptions">
          </el-date-picker>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="存款日期：" prop="ck_at">
          <el-date-picker
            v-model="orderPayments.ck_at"
            type="datetime"
            placeholder="选择日期时间"
            align="right"
            :picker-options="dateOptions">
          </el-date-picker>
        </el-form-item>
      </el-col>
      <el-col :span="8">
        <el-form-item label="收款备注：" prop="remark">
          <el-input type="textarea" :autosize="{ minRows: 1, maxRows: 4}"
            v-model="orderPayments.remark">
          </el-input>
        </el-form-item>
      </el-col>
    </el-row>
    <el-row>
      <div class="form-line-head">
        配送费：
      </div>
    </el-row>
    <el-row :gutter="20">
      <el-col :span="12">
        <el-form-item label="货物配送费：">
          <el-input placeholder="货物配送费" :disabled="true" v-model="orderPayments.delivery_fee">
            <el-button slot="append" @click="handleReCalculate()">重算</el-button>
          </el-input>
        </el-form-item>
      </el-col>
      <el-col :span="12">
        <el-form-item label="司机配送费：">
          <el-input placeholder="司机配送费" :disabled="true" v-model="orderPayments.driver_fee">
            <el-button slot="append" @click="handleReCalculate()">重算</el-button>
          </el-input>
        </el-form-item>
      </el-col>
    </el-row>
    <el-row :gutter="20">
        <el-col :span="4">
            <el-checkbox v-model="selectFirstDriver" :checked="selectFirstDriver">二次配送</el-checkbox>
        </el-col>
        <el-col :span="14" v-show="selectFirstDriver">
          <el-form-item label="首配司机：">
            <el-select v-model="orderPayments.second_driver_id" filterable placeholder="请输入关键词">
              <el-option
                      v-for="item in driverOptions"
                      :key="item.id"
                      :label="item.name"
                      :value="item.id">
              </el-option>
            </el-select>
          </el-form-item>
        </el-col>
    </el-row>
    <el-row :gutter="20">
      <el-col :span="4">
          <el-checkbox v-model="exchangeBottle.is_checked" :checked="exchangeBottle.is_checked">换盖</el-checkbox>
      </el-col>
      <el-col :span="10" v-show="exchangeBottle.is_checked">
        <el-form-item label="兑换档口：">
          <el-select v-model="exchangeBottle.store_id" filterable placeholder="请输入关键词">
          <el-option
                  v-for="item in storeOptions"
                  :key="item.store_id"
                  :label="item.store_name"
                  :value="item.store_id">
          </el-option>
          </el-select>
        </el-form-item>
      </el-col>
      <el-col :span="10" v-show="exchangeBottle.is_checked">
        <el-form-item label="换盖金额：" prop="exhange_amount">
          <el-input placeholder="换盖金额" v-model="exchangeBottle.amount">
            <el-button slot="append" @click="handleExchangeBottle()" :loading="saveExchangeLoading">保存</el-button>
          </el-input>
        </el-form-item>
      </el-col>
    </el-row>
    <el-row :gutter="20" class="form-footer">
        <el-col :span="6" class="form-footer-text">
            录入人：{{orderPayments.jlr}}
        </el-col>
        <el-col :span="6" class="form-footer-text">
            变更人：{{orderPayments.updater}}
        </el-col>
        <el-col :span="6" class="form-footer-text">
            修改日期：{{orderPayments.updated_at}}
        </el-col>
        <el-col :span="6" class="form-footer-text">
            录入状态：{{orderPayments.status | orderPaymentsStatus}}
        </el-col>
    </el-row>
    <el-row :gutter="20" type="flex" class="row-bg" justify="center">
      <el-col :span="6">
        <el-button @click="handleCancel">取 消</el-button>
      </el-col>
      <el-col :span="6">
        <el-button v-if="orderPayments.status != 1" type="primary" @click="handleSaveAndCancel">保存并退出</el-button>
      </el-col>
      <el-col :span="6">
        <el-button v-if="orderPayments.status != 1" type="primary" @click="handleSave">保 存</el-button>
      </el-col>
    </el-row>
  </el-form>
</template>

<script>
import { fetchList, fetchUpdate, fetchCreate } from 'api/restfull';
import { showMsg } from 'utils/index';
import OrderGoodsList from './_orderGoodsList.vue';
export default {
  name: 'MainOrderDetail',
  components: { OrderGoodsList },
  props: {
    mainOrder: {
      type: Object,
      default: null
    },
    inputStaus: {
      tyep: String,
      default: 'edit'
    }
  },
  data() {
    var validateOutPaySn = (rule, value, callback) => {
      if (this.orderPayments.pos > 0 || this.orderPayments.wexin > 0 || this.orderPayments.wexin > 0) {
        if (_.isNull(value)) {
          callback(new Error('请输入刷卡单号'));
        }
      }
      callback();
    };
    return {
      selectFirstDriver: false,
      saveExchangeLoading: false,
      recalculateLoading: false,
      storeOptions: [],
      driverOptions: [],
      goodsListVisible: false,
      exchangeBottle: {
        is_checked: false,
        driver_id: 0,
        store_id: '',
        pay_sn: '',
        amount: 0
      },
      priceRule: [
        { type: 'float', message: '必须为数值', trigger: 'blur' }
      ],
      rules: {
        jk_driver_id: [
          { required: true, message: '请选择交款人' }
        ],
        ck_at: [
          { type: 'date', required: true, message: '请选择存款日期' }
        ],
        desc_remark: [
          { type: 'string', message: '必须为数值', trigger: 'blur' },
          { max: 100, message: '最大长度为 100 个字符', trigger: 'blur' }
        ],
        out_pay_sn: [
          { validator: validateOutPaySn, trigger: 'blur' },
          { type: 'string', max: 100, message: '最大长度在为 100 个字符', trigger: 'blur' }
        ],
        remark: [
          { type: 'string', max: 100, message: '最大长度在为 100 个字符', trigger: 'blur' }
        ],
        qiandan: [
          { type: 'number', message: '必须为数值', trigger: 'blur' }
        ],
        ziti: [
          { type: 'number', message: '必须为数值', trigger: 'blur' }
        ],
        weicha: [
          { type: 'number', message: '必须为数值', trigger: 'blur' }
        ],
        qita: [
          { type: 'number', message: '必须为数值', trigger: 'blur' }
        ],
        pos: [
          { type: 'number', message: '必须为数值', trigger: 'blur' }
        ],
        weixin: [
          { type: 'number', message: '必须为数值', trigger: 'blur' }
        ],
        alipay: [
          { type: 'number', message: '必须为数值', trigger: 'blur' }
        ],
        cash: [
          { type: 'number', message: '必须为数值', trigger: 'blur' }
        ],
        yizhifu: [
          { type: 'number', message: '必须为数值', trigger: 'blur' }
        ]
      },
      dateOptions: {
        shortcuts: [{
          text: '今天',
          onClick(picker) {
            picker.$emit('pick', new Date());
          }
        }, {
          text: '昨天',
          onClick(picker) {
            const date = new Date();
            date.setTime(date.getTime() - 3600 * 1000 * 24);
            picker.$emit('pick', date);
          }
        }, {
          text: '大前天',
          onClick(picker) {
            const date = new Date();
            date.setTime(date.getTime() - 3600 * 1000 * 24 * 2);
            picker.$emit('pick', date);
          }
        }]
      }
    }
  },
  created() {
    this.getDriverOptions();
    this.getStoreOptions()
  },
  computed: {
    orderPayments() {
      return this.mainOrder;
    },
    shifaAmount() {
      return this.getShifaAmount();
    },
    yingshouAmount() {
      return this.getYingshouAmount()
    },
    shishouAmount() {
      return this.getShishouAmount();
    }
  },
  methods: {
    handleExchangeBottle() {
      if (_.isNull(this.orderPayments.jk_driver_id)) {
        this.$message({
          message: '请先选择交款人',
          type: 'error'
        });
        return false;
      }
      this.exchangeBottle.driver_id = this.orderPayments.jk_driver_id;
      if (!this.exchangeBottle.amount) {
        this.$message({
          message: '请输入兑换金额',
          type: 'error'
        });
        return false;
      }
      this.exchangeBottle.pay_sn = this.orderPayments.pay_sn;
      this.saveExchangeLoading = true;
      fetchCreate(this.exchangeBottle, '/exchange_bottles').then(response => {
        this.saveExchangeLoading = false;
        showMsg(response.data)
      })
    },
    handleReCalculate() {
      if (!this.orderPayments.id) {
        this.$message({
          message: '录入之后将自动计算',
          type: 'error'
        });
        return false;
      }
      this.recalculateLoading = true;
      fetchList({ pay_ids: this.orderPayments.pay_id }, '/main_order_payments/re_calculate').then(response => {
        this.recalculateLoading = false
        showMsg(response.data)
      })
    },
    handleSave() {
      this.$refs.orderMainForm.validate(valid => {
        if (valid) {
          fetchCreate(this.orderPayments, '/main_order_payments').then(response => {
            if (!response.data.status) {
              this.$message({
                message: response.data.msg,
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
        } else {
          return false;
        }
      })
    },
    handleCancel() {
      this.$emit('handleCancel');
    }, // 保存并关闭
    handleSaveAndCancel() {
      this.handleSave();
      this.handleCancel();
    },
    toogleGoodsList() {
      this.goodsListVisible = !this.goodsListVisible;
    },
    handleGoodsListChange(change) {
      this.orderPayments.goods_list = change;
      let sum_jushou = 0.0;
      let sum_quehuo = 0.0;
      _.forEach(this.orderPayments.goods_list, goods => {
        if (!_.isNull(goods.payments)) {
          sum_jushou = _.add(goods.payments.jushou_number * goods.goods_price, sum_jushou)
          sum_quehuo = _.add(goods.payments.quehuo_number * goods.goods_price, sum_quehuo)
        }
      });

      this.orderPayments.jushou = sum_jushou
      this.orderPayments.quehuo = sum_quehuo
      this.orderPayments.shifa = this.getShifaAmount()
    },
    handlePreOne() {
      this.$emit('pre');
    },
    handleNextOne() {
      this.$emit('next');
    },
    handleNull() {
      if (_.isNull(this.orderPayments.pd_amount)) {
        this.orderPayments.pd_amount = 0;
      }
      if (_.isNull(this.orderPayments.pos)) {
        this.orderPayments.pos = 0;
      }
      if (_.isNull(this.orderPayments.weixin)) {
        this.orderPayments.weixin = 0;
      }
      if (_.isNull(this.orderPayments.alipay)) {
        this.orderPayments.alipay = 0;
      }
      if (_.isNull(this.orderPayments.yizhifu)) {
        this.orderPayments.yizhifu = 0;
      }
      if (_.isNull(this.orderPayments.cash)) {
        this.orderPayments.cash = 0;
      }
    },
    getShishouAmount() {
      this.handleNull();
      return _.add(_.add(_.add(_.add(_.add(parseFloat(this.orderPayments.pd_amount), parseFloat(this.orderPayments.pos)), parseFloat(this.orderPayments.weixin)), parseFloat(this.orderPayments.alipay)), parseFloat(this.orderPayments.yizhifu)), parseFloat(this.orderPayments.cash))
    },
    getYingshouAmount() {
      return this.orderPayments.shifa - this.orderPayments.qiandan - this.orderPayments.ziti
       - this.orderPayments.weicha - this.orderPayments.qita
    },
    getShifaAmount() {
      return this.orderPayments.goods_amount - this.orderPayments.jushou - this.orderPayments.quehuo
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
    }
  }
}
</script>

<style lang="scss">
.el-row {
    margin-bottom: 5px;
    &:last-child {
      margin-bottom: 0;
    }
  }
.el-col {
  margin-bottom: 15px;
  &:last-child {
    margin-bottom: 0;
  }
}
.form-title {
  text-align: center;
}
.form-line-head {
  background: #e5e9f2;
}
.form-line-head-desc {
  float: right;
}
.form-head-price {
  color: #F7BA2A;
}
.el-form-item {
  margin-bottom: 5px
}
</style>
