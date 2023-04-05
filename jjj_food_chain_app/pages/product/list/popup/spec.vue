<template>
	<view>
		<!-- 商品详情模态框 begin -->
		<modal :show="goodDetailModalVisible" class="good-detail-modal" color="#5A5B5C" width="100%" custom
			padding="0rpx" radius="0" @cancel="closeGoodDetailModal">
			<view class="cover">
				<image v-if="form.detail.product_image" :src="form.detail.product_image" class="image" mode="aspectFill"></image>
			</view>
			<view>
				<view class="good_basic">
					<view class="name text-ellipsis">{{ form.detail.product_name }}</view>
				</view>
			</view>
			<scroll-view class="detail" scroll-y>
				<view class="wrapper">
					<view class="properties">
						<view class="property" v-if="form.detail.spec_type==20">
							<view class="title">
								<text class="name">规格</text>
							</view>
							<view class="values">
								<view class="value" @click="selecedtSpec(value,key)"
									v-for="(value, key) in form.detail.sku" :key="key"
									:class="{'default': value.checked}">
									{{ value.spec_name }}
								</view>
							</view>
						</view>
						<view class="property" v-for="(item,index) in form.detail.product_attr" :key='index'>
							<view class="title">
								<text class="name">{{item.attribute_name}}</text>
							</view>
							<view class="values">
								<view class="value" @click="selectAttr(value,key,index)" v-if="value!=''"
									v-for="(value, key) in item.attribute_value" :key="key"
									:class="{'default': form.show_sku.attr[index]==key}">
									{{ value }}
								</view>
							</view>
						</view>
						<view class="property">
							<view class="title">
								<text class="name">加料</text>
							</view>
							<view class="values">
								<view class="value" @click="selectFeed(item,index)"
									v-for="(item, index) in form.detail.product_feed" :key="index"
									:class="{'default': form.show_sku.feed[index]!=null}">
									+{{ item.feed_name }}
								</view>
							</view>
						</view>
					</view>
				</view>
			</scroll-view>
			<view class="spec-bottom">
				<view class="action">
					<view class="d-c d-a-s">
						<view class="left">
							<view class="price"><text class="f22 fb">￥</text><text class="fb">{{ price }}</text><text v-if="bag_type!=1"><text class="f24 gray9">(￥{{ bag_price }})</text></text>
							</view>
						</view>
						<view class="f22 gray9">{{describe()}}</view>
					</view>
					<view class="btn-group">
						<button type="default" plain class="btn theme-borderbtn" size="min" hover-class="none"
							@click="sub()">
							<view class="icon icon-jian iconfont iconsami-select"></view>
						</button>
						<view class="number">{{ form.show_sku.sum }}</view>
						<button type="primary"  class="btn theme-btn" size="min" hover-class="none"
							@click="add()">
							<view class="icon icon-jia iconfont iconadd-select"></view>
						</button>
					</view>
				</view>
				<view>
					<view class="add-to-cart-btn" @tap="confirmFunc">
						<view>加入购物车</view>
					</view>
				</view>
			</view>
			
			
		</modal>
		<!-- 商品详情模态框 end -->
	</view>
</template>

<script>
	import modal from '@/components/modal/modal'
	export default {
		components: {
			modal
		},
		data() {
			return {
				/*是否可见*/
				Visible: false,
				/*表单对象*/
				form: {
					attr: [],
					product_sku_id: [],
					feed: [],
					detail: {

					},
					show_sku: {
						sku_image: ''
					},
					shop_supplier_id: 0
				},
				/*当前商品总库存*/
				stock: 0,
				/*选择提示*/
				selectSpec: '',
				/*是否打开过多规格选择框*/
				isOpenSpec: false,
				type: '',
				product_price: '',
				feed_price: 0,
				space_name: '',
				attr_name: [],
				feed_name: []
			}
		},
		props: ["goodDetailModalVisible", 'productModel', 'orderType', 'bag_type', 'dinner_type', 'cart_type'],
		computed: {

			bag_price: function() {
				return this.form.detail.bag_price * this.form.show_sku.sum;
			},
			price: function() {
				return (this.feed_price * 1 + this.product_price * 1) * this.form.show_sku.sum;

			},
			/*判断增加数量*/
			isadd: function() {
				return this.form.show_sku.sum >= this.stock || this.form.show_sku.sum >= this.form.detail.limit_num;
			},

			/*判断减少数量*/
			issub: function() {
				return this.form.show_sku.sum <= 1;
			}
		},
		watch: {
			'goodDetailModalVisible': function(n, o) {
				let self = this;
				if (n != o) {
					self.Visible = n;
					self.form = self.productModel;
					self.isOpenSpec = true;
					self.initShowSku();
					if (self.form.detail.sku[0].checked == false) {
						self.selecedtSpec(self.form.detail.sku[0], 0)
					}
					self.form.detail.product_attr.forEach((item, index) => {
						if (!self.form.show_sku.attr[index]) {
							self.selectAttr(item.attribute_value[0], 0, index)
						}
					})
				}
			},
		},
		methods: {
			describe: function() {
				let space_name = this.space_name;
				if (space_name != '') {
					space_name += ';'
				}
				let attr_name = this.attr_name.join(';');
				if (attr_name != '') {
					attr_name += ';'
				}
				let feed_name = this.feed_name.join(',');
				if (feed_name != '') {
					feed_name += ';'
				}

				return space_name + attr_name + feed_name
			},
			/*初始化*/
			initShowSku() {
				this.form.show_sku.sku_image = this.form.detail.product_image;
				this.form.show_sku.product_price = this.form.detail.product_price;
				this.form.show_sku.product_sku_id = [];
				this.form.show_sku.attr = [];
				this.form.show_sku.feed = [];
				this.form.show_sku.feed.length = this.form.detail.product_feed.length;
				this.form.show_sku.line_price = this.form.detail.line_price;
				this.form.show_sku.stock_num = this.form.detail.product_stock;
				this.form.show_sku.sum = 1;
				this.stock = this.form.detail.product_stock;
			},
			closeGoodDetailModal() {
				this.$emit('closeGoodDetailModal')
			},
			/*选择属性*/
			selecedtSpec(item, index) {
				let self = this;
				if (item.checked) {
					item.checked = false;
					self.form.show_sku.product_sku_id[0] = null;
				} else {
					self.form.detail.sku.forEach((sitem, sindex) => {
						sitem.checked = false;
					})
					item.checked = true;
					self.form.show_sku.product_sku_id[0] = item.product_sku_id;
					self.space_name = item.spec_name;
					self.$set(self.form.show_sku, 'product_price', item.product_price)
				}
				if (self.form.show_sku.product_sku_id[0] == null) {
					self.initShowSku();
					return;
				}
				// 更新商品规格信息
				self.updateSpecProduct();
			},
			/*选择属性*/
			selectAttr(item, index, aindex) {
				let self = this;
				self.$set(self.form.show_sku.attr, aindex, index);
				self.attr_name[aindex] = item;
				// 更新商品规格信息
				self.updateSpecProduct();
			},
			/*选择加料*/
			selectFeed(item, index) {
				let self = this;
				if (self.form.show_sku.feed[index] || self.form.show_sku.feed[index] === 0) {
					self.$set(self.form.show_sku.feed, index, null)
					self.feed_price -= item.price * 1;
					let n = self.feed_name.indexOf(item.feed_name);
					if (n > -1) {
						self.feed_name.splice(n, 1)
					}
				} else {
					self.$set(self.form.show_sku.feed, index, index)
					self.feed_price += item.price * 1;
					self.feed_name.push(item.feed_name)

				}
				// 更新商品规格信息 
				self.updateSpecProduct();
			},
			updateSpecProduct() {
				this.product_price = this.form.show_sku.product_price;
			},
			/*商品增加*/
			add() {
				if (this.stock <= 0) {
					return;
				}
				this.form.show_sku.sum++;
			},
			/*商品减少*/
			sub() {
				if (this.stock <= 0) {
					return;
				}
				if (this.form.show_sku.sum < 2) {
					uni.showToast({
						title: '商品数量至少为1',
						icon: 'none',
						duration: 2000
					});
					return false;
				}
				this.form.show_sku.sum--;
			},
			/*确认提交*/
			confirmFunc() {
				if (this.form.show_sku.product_sku_id[0] == null && this.form.detail.spec_type == 20) {
					uni.showToast({
						title: '请选择规格',
						icon: 'none',
						duration: 2000
					});
					return;
				}
				if (this.form.detail.product_attr != null) {
					for (let i = 0; i < this.form.detail.product_attr.length; i++) {
						if (this.form.show_sku.attr[i] == null) {
							uni.showToast({
								title: '请选择属性',
								icon: 'none',
								duration: 2000
							});
							return;
						}
					}
				}
				this.addCart();
			},
			/*加入购物车*/
			addCart() {
				let self = this;
				let feed = [];
				self.form.show_sku.feed.forEach((item, index) => {
					if (item != null) {
						feed.push(item)
					}
				})
				if (feed.length <= 0) {
					feed = ''
				} else {
					feed = feed.join(',')
				}
				let params = {
					product_id: self.form.detail.product_id,
					product_num: self.form.show_sku.sum,
					product_sku_id: self.form.show_sku.product_sku_id[0],
					attr: self.form.show_sku.attr.join(','),
					feed: feed,
					describe: self.describe(),
					price: self.feed_price * 1 + self.product_price * 1,
					bag_price: self.form.detail.bag_price,
					shop_supplier_id: self.form.shop_supplier_id,
					cart_type: self.cart_type,
					dinner_type: self.dinner_type
				}
				console.log(params)
				self._post('order.cart/add', params, function(res) {
					uni.showToast({
						title: res.msg,
						duration: 2000
					});
					let price = self.feed_price * self.form.show_sku.sum + self.product_price * self.form.show_sku
						.sum;
					if (self.$props.bag_type != 1) {
						price += self.form.detail.bag_price * self.form.show_sku.sum;
					}
					console.log(price.toFixed(2))
					self.$emit('closeGoodDetailModal', self.form.show_sku.sum, price.toFixed(2));
				});
			},
		}
	}
</script>

<style lang="scss">
	@import '../menu.scss';
</style>
