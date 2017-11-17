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
        <span class="demonstration">档口</span>
        <select-store :selected="listQuery.store_id" @changeSelect="queryChangeStore"></select-store>
        <span class="demonstration">货品id</span>
        <el-input placeholder="货品id" v-model="listQuery.goods_id" style="width:150px"></el-input>
        <span class="demonstration">货品名称</span>
        <el-input placeholder="货品名称" v-model="listQuery.goods_name" style="width:150px"></el-input>
        <el-button class="filter-item" type="primary" icon="search"
                   @click="handleSearch()">查询</el-button>
        <el-button class="filter-item" type="text"
                   @click="handleSearchMore()">更多</el-button>
      </el-row>
      <div :class="{ 'search-more':isSearchMore }">
        <el-row>
          <span class="demonstration">货品条码</span>
          <el-input placeholder="货品条码" v-model="listQuery.goods_serial" style="width:150px"></el-input>
          <span class="demonstration">计费方式</span>
          <el-select v-model="listQuery.shipping_charging_type">
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
              border fit highlight-current-row
              @sort-change="sortQuery"
              @row-dblclick='handleUpdateDialog'
              :default-sort = "{prop: 'good_id', order: 'descending'}"
              style="width: 100%">
      <el-table-column align="center" type="index" label="序号" width="65">
      </el-table-column>
      <el-table-column prop="store_name" label="档口" width="200" sortable>
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.store_name}}</span>
        </template>
      </el-table-column>
      <el-table-column prop="goods_id" label="SKU" width="150" sortable>
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.goods_id}}</span>
        </template>
      </el-table-column>
      <el-table-column label="货品名称" width="300">
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.goods_name}}</span>
        </template>
      </el-table-column>
      <el-table-column label="条码" width="150">
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.goods_serial}}</span>
        </template>
      </el-table-column>
      <el-table-column label="货品金额" width="100">
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.goods_price}}</span>
        </template>
      </el-table-column>
      <el-table-column label="单位" width="100">
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.g_unit}}</span>
        </template>
      </el-table-column>
      <el-table-column label="计费方式" width="100">
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.shipping_charging_type | chargingTypeName}}</span>
        </template>
      </el-table-column>
      <el-table-column label="单件费用&费率" width="100">
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.shipping_rate}}</span>
        </template>
      </el-table-column>
      <el-table-column label="拆包费（元/件）" width="100">
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.unpack_fee}}</span>
        </template>
      </el-table-column>
      <el-table-column label="司机计费方式" width="100">
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.driver_charging_type | chargingTypeName}}</span>
        </template>
      </el-table-column>
      <el-table-column label="单件费用&费率" width="100">
        <template slot-scope="scope">
          <span class="table-col-text">{{scope.row.driver_rate}}</span>
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

    <el-dialog :title="dialogTextMap[dialogStatus]"
               :visible.sync="dialogFormVisible">
      <el-form class="small-space" :model="formData"
                ref="dialogForm" :rule="validateRules"
               label-position="left" label-width="150px"
               style='width: 500px; margin-left:50px;'>
        <el-form-item label="档口名称：">
          <span class="form-item">{{formData.store_id}}</span>
        </el-form-item>
        <el-form-item label="SKU：">
          <span class="form-item">{{formData.goods_id}}</span>
        </el-form-item>
        <el-form-item label="货品名称：">
          <span class="form-item">{{formData.goods_name}}</span>
        </el-form-item>
        <el-form-item label="货品条码：">
          <span class="form-item">{{formData.goods_serial}}</span>
        </el-form-item>
        <el-form-item label="单价：">
          <span class="form-item">{{formData.goods_price}}</span>
          <span v-if="formData.g_unit">/{{formData.g_unit}}</span>
        </el-form-item>
        <el-form-item label="计费方式：" prop="shipping_charging_type">
          <el-radio-group v-model.number="formData.shipping_charging_type">
            <el-radio class="radio" :label="0">数量</el-radio>
            <el-radio class="radio" :label="1">金额</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="单件费用&费率：" prop="shipping_rate">
          <el-input v-model.number="formData.shipping_rate"></el-input>
        </el-form-item>
        <el-form-item label="拆包费（元/件）：" prop="unpack_fee">
          <el-input v-model.number="formData.unpack_fee"></el-input>
        </el-form-item>
        <el-form-item label="司机计费方式：" prop="driver_charging_type">
          <el-radio-group v-model.number="formData.driver_charging_type">
            <el-radio class="radio" :label="0">数量</el-radio>
            <el-radio class="radio" :label="1">金额</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="单件费用&费率：" prop="driver_rate">
          <el-input v-model.number="formData.driver_rate"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="cancelForm('dialogForm')">取 消</el-button>
        <el-button type="primary" @click="handleUpdate()">确 定</el-button>
      </div>
    </el-dialog>
    <el-dialog title="excel导入"
               :visible.sync="dialogUploadVisible">
      <el-upload
              class="upload-file"
              :headers="uploadHeaders"
              :on-success="uploadSuccess"
              drag
              action="/import/goods_settings"
              name="file">
        <i class="el-icon-upload"></i>
        <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
        <div class="el-upload__tip" slot="tip">只能上传excel文件，且不超过500kb,
          <a href="/goods_settings/download-excel?path=/exports_demo/&name=goods_setting_demo.xls" target="_blank">下载模板</a>
        </div>
      </el-upload>
    </el-dialog>
  </div>
</template>

<script>
  import { fetchList, fetchCreate } from 'api/restfull';
  import { param, showMsg } from 'utils/index'
  import SelectStore from 'components/Selector/SelectStore';

  export default {
    name: 'goods_setting',
    components: { SelectStore },
    data() {
      return {
        listLoading: false,
        isSearchMore: true,
        list: null,
        total: null,
        baseURL: '/goods_settings',
        selectedRows: [],
        listQuery: {
          page: 1,
          per_page: 20,
          sort_by: 'goods_id',
          sort_order: 'descending',
          add_time: undefined,
          store_id: undefined,
          goods_id: undefined,
          goods_name: undefined,
          goods_serial: undefined,
          shipping_charging_type: ''
        },
        typeMap: [
          { label: '全部', value: '' },
          { label: '按数量计费', value: 0 },
          { label: '按金额计费', value: 1 }
        ],
        dialogFormVisible: false,
        dialogUploadVisible: false,
        dialogFormStauts: '',
        dialogStatus: 'update',
        dialogTextMap: {
          update: '编辑',
          create: '新增'
        },
        dialogSearchVisible: false,
        dialogGoodsListVisible: false,
        tableKey: 0,
        formData: {},
        validateRules: {
          shipping_rate: [
            { required: true, type: 'number', message: '必须为数值', trigger: 'blur' },
            { min: 0, max: 1, message: '必须为 0 ~ 1 的小数', trigger: 'blur' }
          ],
          unpack_fee: [
            { type: 'float', message: '必须为数值', trigger: 'blur' }
          ],
          driver_rate: [
            { required: true, type: 'number', message: '必须为数值', trigger: 'blur' },
            { min: 0, max: 1, message: '必须为 0 ~ 1 的小数', trigger: 'blur' }
          ]
        },
        uploadHeaders: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      }
    },
    created() {
      this.formData = {};
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
      }, // 查询
      handleSearch() {
        this.getList();
      },
      handleSearchMore() {
        return this.isSearchMore = !this.isSearchMore
      }, // 排序查询
      cancelForm(formName) {
        this.dialogFormVisible = false;
        this.$refs[formName].resetFields();
      },
      handleUpdateDialog(row) {
        this.formData = Object.assign({}, row);
        this.dialogStatus = 'update';
        this.dialogFormVisible = true;
      },
      handleUpdate() {
        this.$refs.dialogForm.validate(valid => {
          if (valid) {
            fetchCreate(this.formData, this.baseURL).then(response => {
              if (response.status !== 200) {
                this.$message({
                  message: response.data.message,
                  type: 'error'
                });
                return false;
              }
              for (const v of this.list) {
                if (v.goods_id === this.formData.goods_id) {
                  const index = this.list.indexOf(v);
                  this.list.splice(index, 1, this.formData);
                  break;
                }
              }
              this.dialogFormVisible = false;
              this.$notify({
                title: '成功',
                message: '更新成功',
                type: 'success',
                duration: 2000
              });
            })
          } else {
            return false;
          }
        });
      },
      sortQuery(sort) {
        this.listQuery.sort_by = sort.prop;
        this.listQuery.sort_order = sort.order;
        this.handleSearch();
      }, // 查看
      showError(msg) {
        this.$message({
          message: msg,
          type: 'error'
        });
        return false;
      }, // 编辑
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
      uploadSuccess(response) {
        console.log(response);
        showMsg(response);
        const data = response.data;
        if (!response.status) {
          // 跳转下载错误表格
          window.location.href = this.baseURL + '/download-excel?path=' + data.path + '&name=' + data.name
        }
      },
      handleExport() {
        const query = param(this.listQuery)
        window.location.href = '/export/goods_settings?' + query;
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
