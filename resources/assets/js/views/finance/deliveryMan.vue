<template>
  <div class="app-container calendar-list-container">
    <div class="filter-container">
      <span>司机姓名</span>
      <el-input placeholder="司机姓名" style="width: 150px" v-model="listQuery.name">
      </el-input>
      <span>电话</span>
      <el-input placeholder="电话" style="width: 150px" v-model="listQuery.mobile">
      </el-input>

      <el-button class="filter-item" type="primary" icon="search"
        @click="handleFilter">搜索</el-button>
      <el-button class="filter-item" style="margin-left: 10px;"
        @click="handleCreate" type="primary" icon="edit">添加</el-button>
      <el-button class="filter-item" type="primary" icon="document"
        @click="handleDownload">导出</el-button>
    </div>
    <el-table
        :data="list" :key='tableKey' v-loading.body="listLoading"
        border fit highlight-current-row
        @row-dblclick='handleUpdate'
        style="width: 100%">
      <el-table-column align="center" label="序号" width="65">
        <template scope="scope">
          <span>{{scope.row.id}}</span>
        </template>
      </el-table-column>
      <el-table-column
          prop="code"
          label="编码">
      </el-table-column>
      <el-table-column
          prop="name"
          label="司机名称">
      </el-table-column>
      <el-table-column
          prop="mobile"
          label="手机">
      </el-table-column>
      <el-table-column
          prop="description"
          label="备注">
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
      :rules="validateRules" ref="formData"
      label-position="left" label-width="70px"
      style='width: 400px; margin-left:50px;'>
        <el-form-item label="编号" prop="code" :error="errors.code">
          <el-input v-model="formData.code"></el-input>
        </el-form-item>
        <el-form-item label="司机名称" prop="name" :error="errors.name">
          <el-input v-model="formData.name"></el-input>
        </el-form-item>
        <el-form-item label="手机" prop="mobile" :error="errors.mobile">
          <el-input v-model="formData.mobile"></el-input>
        </el-form-item>
        <el-form-item label="备注" prop="description">
          <el-input v-model="formData.description"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="cancelForm('formData')">取 消</el-button>
        <el-button v-if="dialogStatus=='create'" type="primary" @click="create('formData')">确 定</el-button>
        <el-button v-else type="primary" @click="update('formData')">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script>
  import { fetchList, fetchCreate, fetchUpdate, fetchDelete } from 'api/restfull';

  export default {
    name: 'drivers',
    data() {
      return {
        listLoading: true,
        errors: [],
        list: null,
        total: null,
        pagePath: '/drivers',
        listQuery: {
          page: 1,
          per_page: 20,
          mobile: undefined,
          name: undefined
        },
        dialogFormVisible: false,
        dialogStatus: '',
        dialogTextMap: {
          update: '编辑',
          create: '新增'
        },
        tableKey: 0,
        formData: {},
        validateRules: {
          code: [
            { required: true, message: '请输入编码', trigger: 'blur' },
            { min: 1, max: 20, message: '长度在 1 到 20 个字符', trigger: 'blur' }
          ],
          name: [
            { required: true, message: '请输入司机名称', trigger: 'blur' },
            { min: 1, max: 20, message: '长度在 1 到 20 个字符', trigger: 'blur' }
          ],
          mobile: [
            { required: true, message: '请输入手机', trigger: 'blur' }
          ],
          description: [
            { min: 1, max: 50, message: '长度在 1 到 50 个字符', trigger: 'blur' }
          ]
        }
      }
    },
    created() {
      this.resetTemp();
      this.getList();
    },
    methods: {
      getList() {
        this.listLoading = true;
        fetchList(this.listQuery, '/drivers').then(response => {
          this.list = response.data.data;
          this.total = response.data.total;
          this.pagePath = response.data.path;
          this.listLoading = false;
        })
      }, // 搜索
      handleFilter() {
        this.getList();
      }, // 新建
      handleCreate() {
        this.resetTemp();
        this.dialogStatus = 'create';
        this.dialogFormVisible = true;
      },
      handleUpdate(row) {
        this.formData = Object.assign({}, row);
        this.dialogStatus = 'update';
        this.dialogFormVisible = true;
      },
      handleDelete(row) {
        fetchDelete('/drivers').then(response => {
          if (response.status !== 200) {
            this.$message({
              message: response.data.message,
              type: 'error'
            });
            return false;
          }
          this.$notify({
            title: '成功',
            message: '删除成功',
            type: 'success',
            duration: 2000
          });
          const index = this.list.indexOf(row);
          this.list.splice(index, 1);
        })
      },
      create(formName) {
        this.$refs[formName].validate(valid => {
          if (valid) {
            fetchCreate(this.formData, '/drivers').then(response => {
              if (response.status === 422) {
                this.errors = response.data;
                this.$message({
                  message: '表单验证失败',
                  type: 'error'
                });
                return false;
              } else if (response.status !== 200) {
                this.$message({
                  message: response.data.message,
                  type: 'error'
                });
                return false;
              }
              this.formData.id = response.data.id;
              this.formData.code = response.data.code;
              this.formData.name = response.data.name;
              this.formData.mobile = response.data.mobile;
              this.formData.description = response.data.description;
              this.list.unshift(this.formData);
              // 返回通知
              this.dialogFormVisible = false;
              this.$notify({
                title: '成功',
                message: '创建成功',
                type: 'success',
                duration: 2000
              });
            })
          } else {
            return false;
          }
        });
      },
      update(formName) {
        this.$refs[formName].validate(valid => {
          if (valid) {
            fetchUpdate(this.formData, '/drivers/' + this.formData.id).then(response => {
              if (response.status !== 200) {
                this.$message({
                  message: response.data.message,
                  type: 'error'
                });
                return false;
              }
              for (const v of this.list) {
                if (v.id === this.formData.id) {
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
            console.log('error submit!!');
            return false;
          }
        });
      },
      cancelForm(formName) {
        this.dialogFormVisible = false;
        this.$refs[formName].resetFields();
      },
      resetTemp() {
        this.formData = {
          id: undefined,
          code: '',
          name: '',
          mobile: '',
          description: undefined
        };
      },
      handleSizeChange(val) {
        this.listQuery.per_page = val;
        this.getList();
      },
      handleCurrentChange(val) {
        this.listQuery.page = val;
        this.getList();
      },
      handleDownload() {
        const query = param(this.listQuery)
        window.location.href = '/export/driver?' + query;
      }
    }
  }
</script>
