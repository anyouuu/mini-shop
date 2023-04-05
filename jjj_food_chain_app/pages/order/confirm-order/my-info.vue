<template>
	<view class="my_info">
		<!--地址-->
		<view class="add-address d-s-c" @click="addAddress()" v-if="Address==null">
			<view class="icon-box mr10">
				<image class="addree_icon" src="../../../static/icon/address_icon.png" mode=""></image>
			</view>
			<text>请添加收货地址</text>
		</view>

		<view class="address-defalut-wrap" @click="addAddress()" v-else>
			<view class="d-b-c">
				<view class="add-addr">
					<view>
						<image class="addree_icon mr10" src="../../../static/icon/address_icon.png" mode=""></image>
					</view>
					<view>
						<view class="user mb10">
							<text class="name f32">{{Address.name}}</text>
							<text class="tel f26">{{Address.phone}}</text>
						</view>
						<view class="info">
							<text class="province-c-a f26">{{Address.region.province}} {{Address.region.city}} {{Address.region.region}}{{Address.detail}}</text>
						</view>
					</view>
				</view>
				<view class="icon iconfont icon-jiantou ml80">
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {

			}
		},
		props: ['Address', 'exist_address'],
		onLoad() {

		},
		mounted() {

		},
		methods: {
			/*添加地址*/
			addAddress() {
				let url = '/pages/user/address/address?source=order';
				if (!this.exist_address) {
					url = '/pages/user/address/add/add?delta=1';
				}
				uni.navigateTo({
					url: url,
				});
			}
		}
	}
</script>

<style>
	.info {
		font-size: 33rpx;
		line-height: 43rpx;
	}

	.add-addr {
		display: flex;
		align-items: center;
	}

	.tel {
		font-size: 27rpx;
		color: #999999;
	}

	.choose-address {
		width: 88rpx;
		height: 88rpx;
		margin-right: 35rpx;
	}

	.icon-dizhi {
		margin-right: 25rpx;
		color: #ff6633;
		font-size: 60rpx;
	}

	.addree_icon {
		width: 42rpx;
		height: 42rpx;
	}

	.my_info {
		background-color: #FFFFFF;
		margin-top: 16rpx;
	}
</style>
