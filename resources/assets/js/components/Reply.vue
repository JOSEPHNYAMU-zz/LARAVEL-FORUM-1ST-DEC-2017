<template>
    <div class = "panel panel-default" :id="'reply-' + id">
        <div class = "panel-heading">
            <div class="level">
                <h5 class="flex">
                    <a :href = "'/profiles/'+ data.owner.name"
                    v-text="data.owner.name"> </a>
                    said  <span v-text="ago"></span>
                </h5>
                <div v-if="signedIn">
                    <favourites :reply="data"></favourites>
                </div>
            </div>
        </div>
        <div class = "panel-body">
            <div v-if="editing">
                <div class="form-group">
                    <textarea class="form-control" v-model="body"></textarea>
                </div>
                <button class="btn btn-xs btn-primary" @click="update">Update</button>
                <button class="btn btn-xs btn-link" @click="editing = false">Cancel</button>
            </div>
            <div v-else v-text="body"></div>
        </div>
        <div class="panel-footer level" v-if="canUpdate">
            <button class="btn btn-xs mr-1" @click="editing = true" >Edit</button>
            <button class="btn btn-xs btn-danger mr-1" @click="destroy">Delete</button>
        </div>
    </div>
</template>
<script>
    import Favourites from './Favourite.vue';
    import moment from 'moment';

    export default {
        props: ['data'],

        data() {
            return {
                editing: false,
                body: this.data.body,
                id : this.data.id,
            };
        },

        components: {
          'favourites' : Favourites
        },

        computed: {
          signedIn()
          {
              return window.App.signedIn;
          },
          canUpdate()
          {
             return this.authorize(user => this.data.user_id == user.id);
          },
          ago()
          {
              return moment(this.data.created_at).fromNow();
          }
        },

        methods: {
            update() {
                axios.patch('/replies/' + this.data.id, {body: this.body});

                this.editing = false;

                console.log( flash('Updated!'));

                flash('Updated!');
            },

            destroy()
            {
                axios.delete('/replies/' + this.data.id);

                this.$emit('deleted', this.data.id);
            }

        }
    }

</script>
