<template>
  <div class="app-container calendar-list-container">
    <div class="filter-container">
      <el-row :gutter="10" type="flex" justify="left">
        <el-button-group>
          <el-button class="top-button-group" type="text"
                     @click="handleExport()"
          >导出</el-button>
          <el-button class="top-button-group" type="text"
                     @click="handlePrint()"
          >打印</el-button>
        </el-button-group>
      </el-row>
      <el-row :gutter='10'>
        <span class="demonstration">登记时间</span>
        <el-date-picker
            v-model="listQuery.created_at"
            type="datetimerange"
            placeholder="选择时间范围"
            :picker-options="addTimePickerOption">
        </el-date-picker>
        <span class="demonstration">档口</span>
        <select-store :selected="listQuery.store_id" @changeSelect="queryChangeStore"></select-store>
        <span class="demonstration">支付单号</span>
        <el-input placeholder="支付单号" v-model="listQuery.pay_sn" style="width:200px"></el-input>
        <el-button class="filter-item" type="primary" icon="search"
          @click="handleSearch()">查询</el-button>
      </el-row>
    </div>
    <el-table id="print-wrap"
      :data="list" ref="exchangeBottlesTable" v-loading.body="listLoading"
      border fit highlight-current-row
      @selection-change="handleSelectionChange"
      show-summary
      style="width: 100%">
      <el-table-column align="center" type="index" label="序号" width="65">
      </el-table-column>
      <el-table-column
        type="selection"
        prop="id"
        width="55">
      </el-table-column>
      <el-table-column prop="created_at" label="登记时间" width="180">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.created_at}}</span>
        </template>
      </el-table-column>
      <el-table-column prop="created_at" label="档口" width="250">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.store.store_name}}</span>
        </template>
      </el-table-column>
      <el-table-column prop="amount" label="换盖金额" width="150">
      </el-table-column>
      <el-table-column label="经办司机" width="150">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.driver.name}}</span>
        </template>
      </el-table-column>
      <el-table-column label="关联单号" width="200">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.pay_sn}}</span>
        </template>
      </el-table-column>
      <el-table-column label="操作员" width="150">
        <template scope="scope">
          <span class="table-col-text">{{scope.row.creator_name}}</span>
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
  import { pickerOptions, initDateMothRange, param } from 'utils/index'
  import SelectStore from 'components/Selector/SelectStore';

  export default {
    name: 'exhcange_bottles',
    components: { SelectStore },
    data() {
      return {
        listLoading: false,
        list: null,
        total: null,
        baseURL: '/exchange_bottles',
        selectedRows: [],
        listQuery: {
          page: 1,
          per_page: 20,
          created_at: undefined,
          store_id: undefined,
          pay_sn: undefined
        },
        addTimePickerOption: {
          shortcuts: pickerOptions
        }
      }
    },
    created() {
      this.getList();
      this.listQuery.created_at = initDateMothRange()
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
      handleSelectionChange(val) {
        this.selectedRows = val;
      },
      getSelected() {
        let ids = _.map(this.selectedRows, 'id')
        if (_.isEmpty(ids)) {
          this.$message({
            message: '请选择要操作的数据',
            type: 'error'
          });
          return false;
        }
        ids = ids.join(',')
        return { ids };
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
        this.dialogUploadVisible = true;
      }, // 导出
      handleExport() {
        const query = param(this.listQuery)
        window.location.href = '/export/exchange_bottles?' + query;
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
