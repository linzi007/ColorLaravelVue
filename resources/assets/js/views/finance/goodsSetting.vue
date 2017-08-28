<template>
  <div class="app-container calendar-list-container">
    <div class="filter-container">
      <el-row :gutter="10" type="flex" justify="left">
        <el-button-group>
          <el-button class="top-button-group" type="text"
                     @click="handleImport()">导入</el-button>
          <el-button class="top-button-group" type="text"
                     @click="handleExport()"
          >导出</el-button>
          <el-button class="top-button-group" type="text"
                     @click="handlePrint()"
          >打印</el-button>
        </el-button-group>
      </el-row>
      <el-row :gutter='10'>
        <span class="demonstration">订单时间：</span>
        <el-date-picker
                v-model="listQuery.add_time"
                type="datetimerange"
                placeholder="选择时间范围"
                :picker-options="addTimePickerOption">
        </el-date-picker>
        <select-store :selected="listQuery.store_id" @changeSelect="queryChangeStore"></select-driver>
        <el-button class="filter-item" type="primary" icon="search"
                   @click="handleSearch()">查询</el-button>
        <el-button class="filter-item" type="text"
                   @click="handleSearchMore()">更多</el-button>
      </el-row>
      <div :class="{ 'search-more':isSearchMore }">
        <el-row>
          <span class="demonstration">货品id：</span>
          <el-input placeholder="货品id" v-model="listQuery.goods_id" style="width:80px"></el-input>
          <span class="demonstration">货品名称：</span>
          <el-input placeholder="货品id" v-model="listQuery.goods_id" style="width:80px"></el-input>
          <span class="demonstration">货品条码：</span>
          <el-input placeholder="货品id" v-model="listQuery.goods_id" style="width:80px"></el-input>
          <span class="demonstration">计费方式</span>
          <el-select v-model="listQuery.type">
            <el-option
                    v-for="item in typeMap"
                    :key="item.value"
                    :label="item.label"
                    :value="item.value">
            </el-option>
          </el-select>
        </el-row>
      </div>
    </div>
    <el-table id="print-wrap"
              :data="list" ref="goodSettingTable" v-loading.body="listLoading"
              boder fit highlight-current-row
              @sort-change="sortQuery"
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
      <el-table-column label="档口" width="200">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.store_id}}</span>
        </template>
      </el-table-column>
      <el-table-column label="SKU" width="150">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.goods_id}}</span>
        </template>
      </el-table-column>
      <el-table-column label="货品名称" width="300">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.goods_name}}</span>
        </template>
      </el-table-column>
      <el-table-column label="条码" width="150">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.goods_serial}}</span>
        </template>
      </el-table-column>
      <el-table-column label="货品金额" width="100">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.goods_price}}</span>
        </template>
      </el-table-column>
      <el-table-column label="单位" width="100">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.g_unit}}</span>
        </template>
      </el-table-column>
      <el-table-column label="计费方式" width="100">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.shipping_charging_type}}</span>
        </template>
      </el-table-column>
      <el-table-column label="单件费用&费率" width="100">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.shipping_rate}}</span>
        </template>
      </el-table-column>
      <el-table-column label="拆包费（元/件）" width="100">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.unpack_fee}}</span>
        </template>
      </el-table-column>
      <el-table-column label="司机计费方式" width="100">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.driver_charging_type}}</span>
        </template>
      </el-table-column>
      <el-table-column label="单件费用&费率" width="100">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.driver_fee}}</span>
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
  </div>
</template>

<script>
  import { fetchList } from 'api/restfull';
  import mainOrderDetail from './_mainOrderDetail.vue'
  import { pickerOptions, showMsg, initDateMothRange } from 'utils/index'
  import SelectStore from 'components/Selector/SelectStore';

  export default {
    name: 'mainOrder',
    components: { mainOrderDetail, SelectStore },
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
          current_page: 1,
          per_page: 20,
          add_time: undefined,
          store_id: undefined,
          goods_id: undefined,
          goods_name: undefined,
          goods_serial: undefined,
          type: undefined
        },
        statusMap: [
          { label: '全部', value: '' },
          { label: '按数量计费', value: 0 },
          { label: '按金额计费', value: 1 }
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
        let goods_id = _.map(this.selectedRows, 'goods_id')
        if (_.isEmpty(goods_id)) {
          this.$message({
            message: '请选择要操作的数据',
            type: 'error'
          });
          return false;
        }
        goods_id = goods_id.join(',')
        return { goods_id };
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
      },
      handleImport() {
        return false;
      }, // 导出
      handleExport() {
        fetchList(this.listQuery, '/export/goods_settings').then(response => {
          showMsg(response.data)
        })
      }, // 数据保存
      handleSizeChange(val) {
        this.listQuery.per_page = val;
        this.handleSearch();
      },
      handleCurrentChange(val) {
        this.listQuery.current_page = val;
        this.handleSearch();
      },
      queryChangeStore(val) {
        this.listQuery.store_id = val
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
