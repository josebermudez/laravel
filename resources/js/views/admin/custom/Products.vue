<template>
  <div class="main-content">
    <div class="page-header">
      <h3 class="page-title">{{ $t("products.products") }}</h3>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">{{ $t("general.home") }}</a></li>
        <li class="breadcrumb-item"><a href="#">{{ $t("products.products") }}</a></li>
        <li class="breadcrumb-item active">{{ $t("general.all") }}</li>
      </ol>
      <div class="page-actions">
        <a href="#" class="btn btn-primary">
          <i class="icon-fa icon-fa-plus"/> {{ $t("products.actions.new") }}
        </a>
        <button class="btn btn-danger">
          <i class="icon-fa icon-fa-trash"/> {{ $t("products.actions.delete") }}
        </button>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-header">
            <h6>{{ $t("products.labels.all") }}</h6>
            <div class="card-actions" />
          </div>
          <div class="card-body">
            <table-component
              :data="getProducts"
              sort-by="row.name"
              sort-order="desc"
              table-class="table"
			  v-bind:filter-no-results="i18n.table.filterNoResults"
			  v-bind:filter-placeholder="i18n.table.filterPlaceholder"
            >
              <table-column show="name" v-bind:label="i18n.columns.name"/>
              <table-column show="description" v-bind:label="i18n.columns.description"/>
              <table-column show="stock" v-bind:label="i18n.columns.stock"/>
              <table-column
                show="created_at"
                v-bind:label="i18n.columns.registered_on"
                data-type="date:YYYY-MM-DD h:i:s"
              />
              <table-column
                :sortable="false"
                :filterable="false"
                label=""
              >
                <template slot-scope="row">
                  <div class="table__actions">
                    <router-link to="/admin/products/view">
                      <a class="btn btn-default btn-sm">
                        <i class="icon-fa icon-fa-search"/> {{ $t("products.actions.view") }}
                      </a>
                    </router-link>
                    <a
                      class="btn btn-default btn-sm"
                      data-delete
                      data-confirmation="notie"
                      @click="deleteProduct(row.id)"
                    >
                      <i class="icon-fa icon-fa-trash"/> {{ $t("products.actions.delete") }}
                    </a>
                  </div>
                </template>
              </table-column>
            </table-component>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script type="text/babel">
import { TableComponent, TableColumn } from 'vue-table-component'

export default {
  components: {
    TableComponent,
    TableColumn
  },
  data () {
    return {
	  i18n: {
		  columns: {
			stock: this.$t('products.labels.stock'),
			name: this.$t('products.labels.name'),
			description: this.$t('products.labels.description'),
			registered_on: this.$t('general.labels.registered_on'),
		  },
		table: {
			filterNoResults: this.$t('general.labels.no_rows'),
			filterPlaceholder: this.$t('general.labels.filter_place_holder'),
		}
	  }
    }
  },
  methods: {
    async getProducts ({ page, filter, sort }) {
      try {
        const response = await axios.get(`/api/admin/products/get?page=${page}`)

        return {
          data: response.data
        }
      } catch (error) {
        if (error) {
          window.toastr['error']('There was an error', 'Error')
        }
      }
    },
    deleteProduct (id) {
      let self = this
      window.notie.confirm({
        text: i18n.t('greeting', { name: 'kazupon' }),
        cancelCallback: function () {
          window.toastr['info']('Cancel')
        },
        submitCallback: function () {
          self.deleteUserData(id)
        }
      })
    },
    async deleteUserData (id) {
      try {
        let response = await window.axios.delete('/api/admin/users/' + id)
        this.users = response.data
        window.toastr['success']('User Deleted', 'Success')
      } catch (error) {
        if (error) {
          window.toastr['error']('There was an error', 'Error')
        }
      }
    }
  }
}
</script>
