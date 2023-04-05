<template>
	<view class="product-detail pr">
		<!-- #ifdef MP-WEIXIN || APP-PLUS -->
		<view class="header" :style="'padding-top:'+statusBarHeight+'px;'">
			<view class="reg180"  :style="'height:'+titleBarHeight+'px;'">
				<image @click="goback" src="/static/icon/back-jianatou.png" mode="" style="width: 48rpx;height: 48rpx;"></image>
			</view>
		</view>
		<!-- #endif -->
		<scroll-view scroll-y="true" class="scroll-Y" :style="'height:' + scrollviewHigh + 'px;'" v-if="!loadding">
			<!--图片-->
			<view class="product-pic">
				<swiper class="swiper" indicator-active-color="#ffffff" indicator-color='rgba(255,255,255,.3)' :indicator-dots="indicatorDots"
				 :autoplay="autoplay" :interval="interval" :duration="duration">
					<swiper-item v-for="(item, index) in detail.image" :key="index">
						<image :src="item.file_path" mode="aspectFit"></image>
					</swiper-item>
				</swiper>
			</view>
			<!--基本信息-->
			<view class="bg-white m20 p30 br12">
				<view class="price-wrap">
					<view class="d-s-s d-c ww100">
						<view class="d-s-c pr ww100 mb16">
							<view class="new-price">
								<text>¥</text>
								<text class="num">{{detail.product_sku.product_price }}</text>
							</view>
							<!-- <text class="is-user-grade" v-if="detail.is_user_grade == true">会员折扣价</text> -->
							<text class="old-price ">¥{{ detail.product_sku.line_price }}</text>
							<!--分享-->
							<view class="share-box">
								<button @click="showShare">
									<image class="share_img" src="/static/icon/fenxiang.png" mode=""></image>
								</button>
							</view>
						</view>
						<view>
							<text class="already-sale">已售{{ detail.product_sales }}件</text>
						</view>
					</view>
				</view>
				<view class="product-name text-ellipsis-2">
					<view class="store_type" v-if="detail.supplier.store_type==20">自营</view>{{ detail.product_name }}
				</view>
				<view class="product-describe" v-if="detail.selling_point">{{ detail.selling_point }}</view>
			</view>
			<view class="m20 br12 o-h p-0-30 bg-white">
				<!--已选择-->
				<view class="already-choice" :class="detail.server!=''?'border-b-d9':''" v-if="detail.spec_type == 20" @click="openPopup('order')">
					<view class="group-hd">
						<view class="left">
							<text class="min-name gray9">选择：</text>
						</view>
						<view class="flex-1 p-0-20 center-content f28 text-ellipsis o-h">
							{{alreadyChioce}}
						</view>
						<view class="right">
							<text class="icon iconfont icon-jiantou" style="font-size: 22rpx;color: #9A9A9A;"></text>
						</view>
					</view>
				</view>
				<!-- 保障 -->
				<view class="already-choice" @click="showGuarantee" v-if="detail.server!=''">
					<view class="group-hd">
						<view class="left">
							<text class="min-name gray9">服务：</text>
						</view>
						<view class="flex-1 p-0-20 center-content f28 text-ellipsis o-h">
							{{serverList}}
						</view>
						<view class="right">
							<text class="icon iconfont icon-jiantou" style="font-size: 22rpx;color: #9A9A9A;"></text>
						</view>
					</view>
				</view>
			</view>

			<!--评价-->
			<view class="product-comment m20 br12">
				<view class="group-hd">
					<view class="left">
						<text class="min-name f32 fb">评价({{ detail.comment_data_count }})</text>
					</view>
					<view class="right" @click="lookEvaluate(detail.product_id)">
						<text class="more">查看全部</text>
						<text class="icon iconfont icon-jiantou" style="font-size: 22rpx;color: #9A9A9A;"></text>
					</view>
				</view>
				<view class="comment-list" v-if="detail.comment_data_count > 0">
					<view class="item" v-if="index<=1" v-for="(item, index) in detail.commentData" :key="index">
						<view class="cmt-user">
							<view class="left">
								<image class="photo" :src="item.user.avatarUrl" mode="aspectFill"></image>
								<text class="name">{{ item.user.nickName }}</text>
							</view>
							<text class="datetime">{{ item.create_time }}</text>
						</view>
						<view class="mt20 lh150 f24">{{ item.content }}</view>
					</view>
				</view>
			</view>
			<!-- 店铺信息 -->
			<view class="shop_head_info" v-if="store_open">
				<view class="shop_list_body_item_shop">
					<view class="shop_list_body_item_shop_logo">
						<image :src="shop_info.logos" mode=""></image>
					</view>
					<view class="shop_list_body_item_shop_info flex-1">
						<view class="f32 title fb">{{shop_info.name}}</view>
						<view class="f26 brand gray9">主营品牌： {{shop_info.category_name}}</view>
						<view class="f26 sales gray9">销量：{{shop_info.product_sales}}件</view>
					</view>
					<view class="shop_list_body_item_shop_others">
						<view class="f26 collect">店铺评分：<text class="fb">{{shop_info.server_score}}</text></view>
						<button @click="goto_shop()">进店看看</button>
					</view>
				</view>
			</view>
			<!--详情内容-->
			<view class="product-content">
				<view class="group-hd border-b-e">
					<view class="d-s-c">
						<view class="pro_nameline"></view><text class="min-name  f32 fb">商品介绍</text>
					</view>
				</view>
				<view class="content-box" v-html="detail.content"></view>
			</view>
		</scroll-view>

		<!--底部按钮-->
		<view class="btns-wrap">
			<view class="icon-box d-c-c" @click="gotoPage('/pages/index/index')">
				<button class="d-c-c d-c bg-white">
					<text class="btn_btom pr icon iconfont icon-Homehomepagemenu gray3" style="height: 50rpx;line-height: 60rpx;"></text>
					<text class="f22 gray3" style="height: 50rpx;line-height: 40rpx;">首页</text>
				</button>
			</view>
			<template>
				<button v-if="!room_id==true && !is_virtual" type="primary" class="add-cart" @click="openPopup('card')">加入购物车</button>
				<button v-else class="add-cart-no">加入购物车</button>
				<button type="primary" class="buy" @click="openPopup('order')">立即购买</button>
			</template>
		</view>

		<!--购物确定-->
		<spec :isPopup="isPopup" :productModel="productModel" @close="closePopup"></spec>
		<!--底部弹窗-->
		<share :isbottmpanel="isbottmpanel" :product_id="product_id" @close="closeBottmpanel"></share>
		<!-- 保障弹窗 -->
		<guarantee :isguarantee="isguarantee" :server="detail.server" @close="closeGuarantee"></guarantee>
		<!--生成图片-->
		<uniPopup :show="isCreatedImg" type="middle" @hidePopup="hidePopupFunc">
			<view class="d-c-c d-c create-img">
				<image :src="poster_img" mode="widthFix"></image>
				<!-- #ifdef MP -->
				<button class="btn-red mt20" type="default" @click="savePosterImg">保存图片</button>
				<!-- #endif  -->
				<!-- #ifdef H5 -->
				<view class="mt20 f34 red" type="default">长按保存图片</view>
				<!-- #endif -->
			</view>
		</uniPopup>
	</view>
</template>

<script>
	import share from './popup/share.vue';
	import guarantee from '@/components/guarantee.vue';
	import spec from './popup/spec.vue';
	import uniPopup from '@/components/uni-popup.vue';
	import utils from '@/common/utils.js';
	export default {
		components: {
			spec,
			share,
			uniPopup,
			guarantee
		},
		data() {
			return {
				statusBarHeight: 0,
				titleBarHeight: 0,
				store_open: 0,
				/*手机高度*/
				phoneHeight: 0,
				/*可滚动视图区域高度*/
				scrollviewHigh: 0,
				/*是否加载完成*/
				loadding: true,
				/*是否显示面板指示点*/
				indicatorDots: true,
				/*是否知道切换*/
				autoplay: true,
				/*自动切换时间间隔*/
				interval: 2000,
				/*滑动动画时长*/
				duration: 500,
				/*是否确定购买弹窗*/
				isPopup: false,
				/*商品id*/
				product_id: null,
				/*商品详情*/
				detail: {
					product_sku: {},
					show_sku: {
						product_price: '',
						product_sku_id: 0,
						line_price: '',
						stock_num: 0,
						sku_image: ''
					}
				},
				/*商品属性*/
				specData: null,
				/*子级页面传参*/
				productModel: {},

				buyNow: false,
				url: '',
				/*规格数组*/
				productSpecArr: [],
				/*购物车商品数量*/
				cart_total_num: 0,
				/*分享*/
				isbottmpanel: false,
				isguarantee: false,
				/*是否生成图片*/
				isCreatedImg: false,
				poster_img: '',
				/*是否打开客服*/
				isMpservice: false,
				/*已经选择的规格*/
				alreadyChioce: '',
				shop_info: '', //店铺信息
				isfollow: '', //是否收藏商品
				shop_supplier_id: '', //店铺ID
				serverList: '', //保障
				service_open: 0,
				service_type: 0,
				user_id: 0,
				is_virtual: 1
			};
		},
		onLoad(e) {
			/*商品id*/
			this.GetStatusBarHeight();
			let scene = utils.getSceneData(e);
			this.user_id = uni.getStorageInfoSync('user_id')
			this.room_id = e.room_id
			this.product_id = e.product_id ? e.product_id : scene.gid;
			//#ifdef H5
			if (this.isWeixin()) {
				this.url = window.location.href;
			}
			//#endif
		},
		onReady() {
			this.init();
			/*获取产品详情*/
			this.getData();
		},
		methods: {
			GetStatusBarHeight() {
				// #ifdef MP-WEIXIN
				let that = this;
				const SystemInfo = uni.getSystemInfoSync();
				let statusBarHeight = SystemInfo.statusBarHeight;
				this.statusBarHeight = uni.getMenuButtonBoundingClientRect().top;
				this.titleBarHeight = uni.getMenuButtonBoundingClientRect().height;
				// #endif
				// #ifndef MP-WEIXIN
				const SystemInfo = uni.getSystemInfoSync();
				this.statusBarHeight = SystemInfo.statusBarHeight;
				this.titleBarHeight = 30;
				// #endif
			},
			/*初始化*/
			init() {
				let _this = this;
				uni.getSystemInfo({
					success(res) {
						_this.phoneHeight = res.windowHeight;
						// 计算组件的高度
						let view = uni.createSelectorQuery().select('.btns-wrap');
						view.boundingClientRect(data => {
							let h = _this.phoneHeight - data.height;
							//#ifdef MP-WEIXIN
							h = _this.phoneHeight
							//#endif
							console.log(_this.phoneHeight)
							console.log(data.height)
							_this.scrollviewHigh = h;
						}).exec();
					}
				});
			},

			/*获取数据*/
			getData() {
				let self = this;
				let product_id = self.product_id;
				uni.showLoading({
					title: '加载中'
				});
				self._get(
					'product.product/detail', {
						product_id: product_id,
						url: self.url,
						visitcode: self.getVisitcode()
					},
					function(res) {
						self.shop_supplier_id = res.data.detail.supplier.shop_supplier_id;
						self.shop_info = res.data.detail.supplier;
						self.isfollow = res.data.detail.isfollow;
						self.service_open = res.data.service_open;
						self.is_virtual = res.data.detail.is_virtual;
						self.cart_total_num = res.data.cart_total_num;
						self.store_open = res.data.store_open;
						/*详情内容格式化*/
						res.data.detail.content = utils.format_content(res.data.detail.content);

						// 初始化商品多规格
						if (res.data.detail.spec_type == 20) {
							self.initSpecData(res.data.specData);
						}

						self.detail = res.data.detail;
						self.getServer();
						// 配置微信分享参数
						//#ifdef H5
						if (self.url != '') {
							let params = {
								product_id: self.product_id
							};
							self.configWx(res.data.share.signPackage, res.data.share.shareParams, params);
						}
						//#endif
						self.loadding = false;
						uni.hideLoading();
					}
				);
			},
			getServer() {
				let self = this;
				let serverList = [];
				self.detail.server.forEach((item, index) => {
					serverList.push(item.name)
				})
				self.serverList = serverList.join('·')
			},
			/*多规格商品*/
			initSpecData(data) {
				for (let i in data.spec_attr) {
					for (let j in data.spec_attr[i].spec_items) {
						data.spec_attr[i].spec_items[j].checked = false;
					}
				}
				this.specData = data;
				if (this.specData.spec_attr) {
					this.specData.spec_attr.forEach(item => {
						this.alreadyChioce += item.group_name;
						this.alreadyChioce += ' / ';
					});
					this.alreadyChioce = this.alreadyChioce.replace(/(\s\/\s)$/gi, '');
				}
			},

			/*选规格*/
			openPopup(e) {
				let obj = {
					specData: this.specData,
					detail: this.detail,
					productSpecArr: this.specData != null ? new Array(this.specData.spec_attr.length) : [],
					room_id: this.room_id == '' ? '0' : this.room_id,
					show_sku: {
						sku_image: '',
						seckill_price: 0,
						product_sku_id: 0,
						line_price: 0,
						seckill_stock: 0,
						seckill_product_sku_id: 0,
						sum: 1,

					},
					type: e
				};
				this.productModel = obj;
				this.isPopup = true;
			},

			/*关闭弹窗*/
			closePopup(e, cart_total_num) {
				this.isPopup = false;
				if (e && e.spec_attr) {
					this.alreadyChioce = '';
					let has = '已选：';
					let noone = '';
					e.spec_attr.forEach(item => {
						if (item.spec_items) {
							let h = '';
							for (let i = 0; i < item.spec_items.length; i++) {
								let child = item.spec_items[i];
								if (child.checked) {
									h = child.spec_value + ' / ';
									break;
								}
							}
							if (h != '') {
								has += h;
							} else {
								noone += item.group_name;
							}
						}
					});
					if (noone != '') {
						this.alreadyChioce = noone;
					} else {
						has = has.replace(/(\s\/\s)$/gi, '');
						this.alreadyChioce = has;
					}
				}
				if (cart_total_num) {
					this.cart_total_num = cart_total_num;
				}
			},

			/*查看更多评价*/
			lookEvaluate(product_id) {
				uni.navigateTo({
					url: '/pages/product/detail/look-evaluate/look-evaluate?product_id=' + product_id
				});
			},

			/*分享*/
			onShareAppMessage() {
				let self = this;
				// 构建页面参数
				let params = self.getShareUrlParams({
					product_id: self.product_id
				});
				return {
					title: self.detail.product_name,
					path: '/pages/product/detail/detail?' + params
				};
			},
			goback() {
				uni.navigateBack()
			},
			/*跳转购物车*/
			gotocart() {
				this.gotoPage('/pages/cart/cart')
			},

			//关闭分享
			closeBottmpanel(data) {
				this.isbottmpanel = false;
				if (data.type == 2) {
					this.poster_img = data.poster_img;
					this.isCreatedImg = true;
				}
			},
			closeGuarantee() {
				this.isguarantee = false;
			},
			showGuarantee() {
				this.isguarantee = true;
			},
			//分享按钮
			showShare() {
				let self = this;
				//#ifndef APP-PLUS
				self.isbottmpanel = true;
				//#endif
				//#ifdef APP-PLUS
				self.appParams.title = self.detail.product_name;
				self.appParams.summary = self.detail.product_name;
				// 构建页面参数
				let params = self.getShareUrlParams({
					product_id: self.product_id
				});
				self.appParams.path = '/pages/product/detail/detail?' + params;
				self.appParams.image = self.detail.image[0].file_path;
				self.isAppShare = true;
				//#endif
			},
			//关闭分享
			closeAppShare(data) {
				this.isAppShare = false;
			},
			//关闭生成图片
			hidePopupFunc() {
				this.isCreatedImg = false;
			},

			//保存海报图片
			savePosterImg() {
				let self = this;
				uni.showLoading({
					title: '加载中'
				});
				// 下载海报图片
				uni.downloadFile({
					url: self.poster_img,
					success(res) {
						uni.hideLoading();
						// 图片保存到本地
						uni.saveImageToPhotosAlbum({
							filePath: res.tempFilePath,
							success(data) {
								uni.showToast({
									title: '保存成功',
									icon: 'success',
									duration: 2000
								});
								// 关闭商品海报
								self.isCreatedImg = false;
							},
							fail(err) {
								if (err.errMsg === 'saveImageToPhotosAlbum:fail auth deny') {
									uni.showToast({
										title: '请允许访问相册后重试',
										icon: 'none',
										duration: 1000
									});
									setTimeout(() => {
										uni.openSetting();
									}, 1000);
								}
							},
							complete(res) {
								console.log('complete');
							}
						});
					}
				});
			},
			
			//跳转店铺首页
			goto_shop() {
				let self = this;
				uni.navigateTo({
					url: '/pages/shop/shop?shop_supplier_id=' + self.shop_supplier_id
				})
			},
		}
	};
</script>

<style lang="scss">
	.product-detail {
		padding-bottom: 90rpx;
	}

	.product-detail .product-pic,
	.product-detail .product-pic .swiper,
	.product-detail .product-pic image {
		width: 750rpx;
		height: 750rpx;
	}

	.product-detail .price-wrap {
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.product-detail .price-wrap .left {
		display: flex;
		justify-content: flex-start;
		align-items: flex-end;
	}

	.product-detail .price-wrap .new-price {
		color: $dominant-color;
		font-size: 30rpx;
		font-weight: bold;
		margin-right: 14rpx;
	}

	.product-detail .price-wrap .new-price .num {
		padding: 0 4rpx;
		font-size: 40rpx;
	}

	.product-detail .price-wrap .old-price {
		font-size: 26rpx;
		color: #999999;
		text-decoration: line-through;
	}

	.product-detail .price-wrap .is-user-grade {
		padding: 0 10rpx;
		border-radius: 12rpx;
		margin-left: 10rpx;
		border: 2rpx solid $dominant-color;
		color: $dominant-color;
	}

	.product-detail .already-sale {
		font-size: 26rpx;
		color: #999999;
	}

	.product-detail .product-name {
		padding-top: 26rpx;
		font-size: 32rpx;
		font-weight: bold;
		color: #333333;

	}

	.product-detail .product-describe {
		padding: 18rpx;
		line-height: 40rpx;
		font-size: 26rpx;
		color: #666666;
		background-color: #f2f2f2;
		border-radius: 12rpx;
		word-break: break-all;
		margin-top: 28rpx;
	}

	.already-choice {
		background: #ffffff;
	}

	.already-choice .center-content {
		line-height: 90rpx;
	}

	.product-comment,
	.product-content {
		margin-top: 20rpx;
		background: #ffffff;
	}

	.product-content .content-box p image {
		width: 100%;
	}

	.product-content .content-box {
		font-size: 36rpx;
	}

	.btns-wrap {
		position: fixed;
		height: 100rpx;
		right: 0;
		bottom: 0;
		left: 0;
		display: flex;
		background: #ffffff;
		align-items: center;
	}

	.btns-wrap .icon-box {
		width: 80rpx;
		height: 100rpx;
	}

	.btns-wrap .icon-box .iconfont {
		font-size: 40rpx;
		color: #888888;
	}

	.btns-wrap .icon-box .iconfont .num {
		position: absolute;
		top: 10rpx;
		left: 50%;
		height: 30rpx;
		min-width: 30rpx;
		overflow: hidden;
		line-height: 32rpx;
		border-radius: 15rpx;
		font-size: 20rpx;
		color: #ffffff;
		background: red;
	}

	.btns-wrap button,
	.btns-wrap button:after {
		height: 100rpx;
		line-height: 100rpx;
		margin: 0;
		padding: 0;
		flex: 1;
		border-radius: 0;
		border: 0;
	}

	.btns-wrap button.add-cart {
		background: linear-gradient(0deg, #ffa945 0%, #ff8439 100%);
		font-size: 32rpx;
		width: 220rpx;
		height: 80rpx;
		line-height: 80rpx;
		border-top-left-radius: 40rpx;
		border-bottom-left-radius: 40rpx;
		margin-left: 10rpx;
	}

	.btns-wrap button.add-cart-no {
		background: #CCCCCC;
		font-size: 32rpx;
		width: 220rpx;
		height: 80rpx;
		line-height: 80rpx;
		border-top-left-radius: 40rpx;
		border-bottom-left-radius: 40rpx;
		color: #FFFFFF;
		margin-left: 10rpx;
	}

	.btns-wrap button.buy {
		background: linear-gradient(0deg, #ff4444 0%, #f6220d 100%);
		font-size: 32rpx;
		width: 220rpx;
		height: 80rpx;
		line-height: 80rpx;
		border-top-right-radius: 40rpx;
		border-bottom-right-radius: 40rpx;
		margin-right: 30rpx;
	}

	.red {
		color: #f6220c !important;
	}

	.shoucang-box {
		position: fixed;
		padding-right: 10rpx;
		width: 80rpx;
		height: 80rpx;
		right: 0;
		bottom: 270rpx;
		display: flex;
		justify-content: center;
		align-items: center;
		border-radius: 16rpx 0 0 16rpx;
		background: rgba(0, 0, 0, 0.8);
	}

	.shoucang-box button {
		padding: 0;
		background: 0;
		line-height: 60rpx;
	}

	.shoucang-box .iconfont {
		margin-bottom: 10rpx;
		font-size: 50rpx;
		color: #ffffff;
		position: relative;
		top: 5rpx;
	}

	.share-box {
		position: absolute;
		width: 60rpx;
		height: 60rpx;
		right: 0;
		bottom: -16rpx;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.share-box button {
		padding: 0;
		background: 0;
		line-height: 60rpx;
		border-radius: 0;
	}

	.share-box .iconfont {
		margin-bottom: 10rpx;
		font-size: 50rpx;
		color: #ffffff;
	}

	.create-img {
		width: 100%;
		padding: 20rpx;
		box-sizing: border-box;
	}

	.create-img image {
		width: 100%;
	}

	.create-img button {
		width: 100%;
		height: 88rpx;
		line-height: 88rpx;
		border-radius: 44rpx;
		box-shadow: 0 8rpx 16rpx 0 rgba(226, 35, 26, 0.6);
	}

	.shop_head_info {
		margin: 20rpx;
		padding: 30rpx;
		box-sizing: border-box;
		background-color: white;
		border-radius: 12rpx;
	}

	.shop_list_body_item_shop {
		width: 100%;
		height: 120rpx;
		display: flex;
		justify-content: space-between;
	}

	.shop_list_body_item_shop_logo {
		width: 120rpx;
		height: 120rpx;
	}

	.shop_list_body_item_shop_logo image {
		width: 100%;
		height: 100%;
		background-color: rgba(0, 0, 0, 0.1);
		border-radius: 12rpx;
	}

	.shop_list_body_item_shop_info {
		box-sizing: border-box;
		margin-left: 20rpx;
		padding-top: 0;
		display: flex;
		justify-content: space-between;
		flex-direction: column;
	}

	.shop_list_body_item_shop_others {
		box-sizing: border-box;
		display: flex;
		justify-content: space-between;
		flex-direction: column;
		text-align: right;
		padding-top: 0;
	}

	.shop_list_body_item_shop_others button {
		width: 160rpx;
		height: 60rpx;
		border: 1rpx solid #F6220C;
		border-radius: 30rpx;
		line-height: 60rpx;
		font-size: 26rpx;
		font-family: PingFang SC;
		font-weight: 500;
		color: #F6220C;
		text-align: center;
		padding: 0;
		background-color: #ffffff;
	}

	.h1 {
		font-size: 32rpx;
	}

	.h2 {
		font-size: 28rpx;
	}

	.h3 {
		font-size: 24rpx;
	}

	.h4 {
		font-size: 20rpx;
	}

	.h5 {
		font-size: 16rpx;
	}

	.h6 {
		font-size: 12rpx;
	}

	.collect text {
		color: #f6220c;
	}

	.store_type {
		display: inline-block;
		background-color: #f6220c;
		color: #FFFFFF;
		border-radius: 7rpx;
		font-weight: 200;
		height: 35rpx;
		line-height: 35rpx;
		font-size: 21rpx;
		padding: 0 10rpx;
		margin-right: 20rpx;
	}

	.share_img {
		width: 30rpx;
		height: 30rpx;
		margin: 0 auto;
		margin-bottom: 4rpx;
	}

	.share_text {
		line-height: 34rpx;
	}

	.reg180 {
		padding-right: 20rpx;
		text-align: center;
		transform: rotateY(180deg);
		display: flex;
		justify-content: flex-end;
		align-items: center;
	}

	.header {
		z-index: 99;
		position: fixed;
		height: 30px;
		line-height: 30px;
		top: 0;
		left: 0;
		width: 100%;
		padding-top: var(--status-bar-height);
	}

	.header .reg180 .icon-jiantou {
		color: #FFFFFF;
		background: rgba($color: #000000, $alpha: 0.6);
		display: block;
		width: 44rpx;
		height: 44rpx;
		line-height: 44rpx;
		border-radius: 50%;
	}

	.btn_btom {
		height: 90rpx;
		line-height: 45rpx;
	}

	.btnname {
		position: absolute;
		bottom: -16px;
		left: 0;
		right: 0;
	}

	.icon-dianpu1 {
		color: #333333;
	}

	.icon-kefu2 {
		color: #333333;
	}

	.pro_nameline {
		width: 4rpx;
		height: 24rpx;
		background-color: #f6220c;
		margin-right: 12rpx;
	}
</style>
