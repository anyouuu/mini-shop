<template>
	<view>
		<diy style="position: relative;" :diyItems="items"></diy>
	</view>
</template>

<script>
	import diy from '@/components/diy/diy.vue';
	import utils from '@/common/utils.js';
	export default {
		components: {
			diy
		},
		data() {
			return {
				user: '',
				/*是否正在加载*/
				loadding: true,
				listData: [],
				url: '',
				longitude: 0,
				latitude: 0,
				delivery_set: [],
				items: []
			};
		},
		mounted() {
			let self = this;
			//#ifdef H5
			if (this.isWeixin()) {
				this.url = window.location.href;
			}
			//#endif
			self.getData();
		},
		onLoad(e) {
			let scene = utils.getSceneData(e);
			let s_id = e.shop_supplier_id ? e.shop_supplier_id : scene.sid;
			if (s_id) {
				uni.setStorageSync('selectedId', s_id);
			}
		},
		methods: {
			getData() {
				let self = this;
				uni.showLoading({
					title: '加载中'
				});
				self._post(
					'index/index', {
						url: self.url,
						longitude: '0',
						latitude: '0'
					},
					function(res) {
						//#ifdef MP-WEIXIN
						if (res.data.getPhone) {
							uni.navigateTo({
								url: '/pages/login/bindmobile'
							});
							return;
						}
						//#endif
						if (res.data.signPackage) {
							uni.setStorageSync('sign', res.data.signPackage);
						}
						if (uni.getStorageSync('selectedId') == '') {
							uni.setStorageSync('selectedId', res.data.supplier ? res.data.supplier.shop_supplier_id :
								0);
						}
						self.items = res.data.data.items;
						self.user = res.data.user;
						self.delivery_set = res.data.supplier.delivery_set;
						uni.hideLoading();
					}
				);
			},
			/*分享当前页面*/
			onShareAppMessage() {
				let self = this;
				return {
					title: self.page.params.share_title,
					path: '/pages/index/index?' + self.getShareUrlParams()
				};
			},
		}
	};
</script>

<style lang="scss">
	/* #ifdef H5 */
	page {
		height: auto;
		min-height: 100%;
	}

	/* #endif */
	.banner {
		position: relative;
		width: 100%;
		height: 600rpx;

		.bg {
			width: 100%;
			height: 600rpx;
		}

		.intro {
			position: absolute;
			top: calc(50rpx + var(--status-bar-height));
			left: 40rpx;
			color: #ffffff;
			display: flex;
			flex-direction: column;

			.greet {
				font-size: $font-size-lg;
				margin-bottom: 10rpx;
			}

			.note {
				font-size: $font-size-sm;
			}
		}
	}

	.content {
		padding: 0 30rpx;
	}

	.entrance {
		position: relative;
		margin-top: -80rpx;
		margin-bottom: 30rpx;
		border-radius: 10rpx;
		background-color: #ffffff;
		box-shadow: $box-shadow;
		padding: 40rpx 0;
		display: flex;
		align-items: center;
		justify-content: center;

		.item {
			flex: 1;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			position: relative;

			&:nth-child(1):after {
				content: '';
				position: absolute;
				width: 1rpx;
				background-color: #ddd;
				right: 0;
				height: 100%;
				transform: scaleX(0.5) scaleY(0.8);
			}

			.icon {
				width: 120rpx;
				height: 120rpx;
				margin: 28rpx;
			}

			.title {
				font-size: 40rpx;
				color: $text-color-base;
				font-weight: 600;
			}

			.content {
				font-size: 28rpx;
				color: $text-color-grey;
				font-weight: 400;
			}
		}
	}

	.info {
		position: relative;
		margin-bottom: 30rpx;
		border-radius: 10rpx;
		background-color: #ffffff;
		box-shadow: $box-shadow;
		padding: 30rpx;
		display: flex;
		align-items: center;
		justify-content: center;

		.integral_section {
			flex: 1;
			display: flex;
			flex-direction: column;
			justify-content: center;

			.top {
				display: flex;
				align-items: center;

				.title {
					color: $text-color-base;
					font-size: $font-size-base;
					margin-right: 10rpx;
				}

				.value {
					font-size: 44rpx;
					font-weight: bold;
				}
			}

			.bottom {
				font-size: $font-size-sm;
				color: $text-color-assist;
				display: flex;
				align-items: center;
			}
		}

		.qrcode_section {
			color: $color-primary;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			font-size: $font-size-sm;

			image {
				width: 40rpx;
				height: 40rpx;
				margin-bottom: 10rpx;
			}
		}
	}

	.navigators {
		width: 100%;
		margin-bottom: 20rpx;
		border-radius: 10rpx;
		background-color: #ffffff;
		box-shadow: $box-shadow;
		padding: 20rpx;
		display: flex;
		align-items: stretch;

		.left {
			width: 340rpx;
			margin-right: 20rpx;
			display: flex;
			padding: 0 20rpx;
			flex-direction: column;
			font-size: $font-size-sm;
			color: $text-color-base;
			background-color: #f2f2e6;

			.grid {
				height: 50%;
				display: flex;
			}
		}

		.right {
			width: 290rpx;
			display: flex;
			flex-direction: column;

			.tea-activity,
			.member-gifts {
				width: 100%;
				display: flex;
				padding: 20rpx;
				font-size: $font-size-sm;
				color: $text-color-base;
				align-items: center;
				position: relative;
			}

			.tea-activity {
				background-color: #fdf3f2;
				margin-bottom: 20rpx;
			}

			.member-gifts {
				background-color: #fcf6d4;
			}

			.right-img {
				flex: 1;
				position: relative;
				margin-left: 20rpx;
				margin-right: -20rpx;
				margin-bottom: -20rpx;
				display: flex;
				align-items: flex-end;

				image {
					width: 100%;
				}
			}
		}

		.mark-img {
			width: 30rpx;
			height: 30rpx;
			margin-right: 10rpx;
		}

		.yzclh-img {
			height: 122.96rpx;
			width: 214.86rpx;
		}
	}

	.member-news {
		width: 100%;
		margin-bottom: 30rpx;

		.header {
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 20rpx 0;

			.title {
				font-size: $font-size-lg;
				font-weight: bold;
			}

			.iconfont {
				font-size: 52rpx;
				color: $text-color-assist;
			}
		}

		.list {
			width: 100%;
			display: flex;
			flex-direction: column;

			.item {
				width: 100%;
				height: 240rpx;
				position: relative;

				image {
					width: 100%;
					height: 100%;
					border-radius: 8rpx;
				}

				.title {
					position: relative;
					font-size: 32rpx;
					font-weight: 500;
					width: 100%;
					top: -70rpx;
					left: 16rpx;
					color: #ffffff;
				}
			}
		}
	}
</style>
