<template>
  <div id="app">
    <!--<div style="max-width: 1260px;margin:0 auto;border:1px solid red;" class="mh-100 h-100">-->
      <div class="d-flex flex-column h-100">
        <section class="pHeader bg-dark p-2">
          <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <div class="navbar-collapse collapse w-100 order-1 order-md-0 dual-collapse2">
              <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                  <a class="nav-link" href="#">Installierte Plugins</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="https://store.shopware.com/net-inventors-gmbh.html?=&p=1" target="_blank">weitere
                    Plugins</a>
                </li>
                <li class="nav-item" style="display: none;">
                  <a class="nav-link" href="#">Support</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link"
                     href="https://support.netinventors.de/hc/de/categories/115000443709-Shopware-Plugins"
                     target="_blank">FAQ</a>
                </li>
              </ul>
            </div>
            <div class="mx-auto order-0">
              <a href="https://www.netinventors.de" class="navbar-brand mx-auto logo brand"><img
                src="https://cdn.netinventors.de/corporate/logo_net_inventors_white.svg" alt=""></a>
              <button class="navbar-toggler" type="button" data-toggle="collapse" data-target=".dual-collapse2">
                <span class="navbar-toggler-icon"></span>
              </button>
            </div>
            <div class="navbar-collapse collapse w-100 order-3 dual-collapse2" id="socialNav">
              <ul class="navbar-nav ml-auto ">
                <li class="nav-item"><a href="https://www.facebook.com/shopinventors" target="_blank"
                                        class="nav-link faceBook">
                  <i class="fab fa-youtube"></i>
                </a></li>
                <li class="nav-item"><a href="https://www.youtube.com/channel/UCDKADxHG1YuTIYHjyGtL61A" target="_blank"
                                        class="nav-link YouTube">
                  <i class="fab fa-youtube-square"></i>
                </a></li>
                <li class="nav-item"><a
                  href="https://www.xing.com/companies/netinventors-agenturf%C3%BCrdigitalemediengmbh" target="_blank"
                  class="nav-link xing">
                  <i class="fab fa-xing-square"></i>
                </a></li>
                <li class="nav-item"><a href="https://gitlab.netinventors.de" target="_blank" class="nav-link Gitlab">
                  <i class="fab fa-gitlab"></i>
                </a></li>
                <li class="nav-item"><a href="https://twitter.com/NetInventors" target="_blank"
                                        class="nav-link Twitter">
                  <i class="fab fa-twitter-square"></i>
                </a></li>
                <li class="nav-item"><a href="https://www.instagram.com/netinventors/" target="_blank"
                                        class="nav-link Instagram">
                  <i class="fab fa-instagram"></i>
                </a></li>
              </ul>
            </div>
          </nav>
        </section>
        <section class="pContent flex-grow-1 p-2">
          <h2 class="card-title">Installierte Plugins</h2>
          <b-table striped hover :items="plugins" :fields="fields">
            <template slot="show_details" slot-scope="row">
              <b-button size="sm" @click.stop="row.toggleDetails" class="mr-2"
                        v-if="row.item._rowVariant === 'danger' && row.item.files.length > 0">
                {{ row.detailsShowing ? 'Hide' : 'Show'}} Details
              </b-button>
            </template>
            <template slot="row-details" slot-scope="row">
              <b-list-group>
                <b-list-group-item :key="file.id" v-for="file in row.item.files">{{file}}</b-list-group-item>
              </b-list-group>
            </template>
          </b-table>
        </section>
        <section class="pFooter bg-dark p-2">
          <div class="copyNote text-center">
            &copy;2009 - 2019 <a href="https://www.netinventors.de" class="">Net Inventors GmbH</a> - Made with
            <i class="fas fa-heart" style="color: red;"></i>
            in Hamburg
          </div>
        </section>
      </div>
    </div>
  <!--</div>-->
</template>

<script>
import axios from 'axios'

export default {
  name: 'App',
  props: ['swConfig'],
  data () {
    let plugins = []
    axios.get(this.swConfig.urls.getPluginList).then(response => {
      const list = response.data.pluginList
      const modified = list.modified.map(row => {
        return {
          plugin_name: row.name,
          files: row.files,
          version: row.version,
          _rowVariant: 'danger',
          status: this.swConfig.snippets.status_modified
        }
      })
      const missing = list.missing.map(row => {
        return {
          plugin_name: row.name,
          version: row.version,
          _rowVariant: 'warning',
          status: this.swConfig.snippets.status_missing
        }
      })
      const unmodified = list.unmodified.map(row => {
        return {
          plugin_name: row.name,
          version: row.version,
          _rowVariant: 'success',
          status: this.swConfig.snippets.status_unmodified
        }
      })

      plugins.push.apply(plugins, Array.concat(modified, missing, unmodified))
    })

    return {plugins, fields: ['plugin_name', 'version', 'status', 'show_details']}
  }
}
</script>

<style type="scss">
  html, body {
  }

  .pHeader {
    background-color: black;
  }

  .pContent {

  }

  .pFooter {
    background-color: black;
    color: white;

    .logo.brand img {
      height: 16px;
    }
  }

  .logo {
    &.brand {
      display: inline-block;

      img {
        display: inline-block;
        height: 30px;
        width: auto;
      }
    }
  }

  .copyNote {
    font-size: 12px;

    a {
      color: white;
    }

    .fa-heart {
      color: #bf0000;
    }
  }

  #socialNav {
    .nav-link {
      color: white;
      font-size: 20px;

      &:hover {
        color: rgba(255, 255, 255, 0.8)
      }
    }
  }

  .table .thead-dark th {
    background-color: #343a40 !important;
  }
</style>
