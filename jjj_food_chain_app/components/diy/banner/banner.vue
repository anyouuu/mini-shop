<template>
	<view>
		<view class="diy-banner-box">
			<swiper class="swiper"  :indicator-active-color="indicatorActiveColor"
				:autoplay="autoplay" :interval="interval" :duration="duration">
				<swiper-item v-for="(item,index) in itemData.data" :key="index" @click="gotoPages(item)">
					<image :src="item.imgUrl"></image>
				</swiper-item>
			</swiper>
		</view>
		<view class="diy-banner-xf d-b-c">
			<view class="banner-xf-box" @click="gotoPage('/'+itemData.params.nav[0].navlinkUrl)">
				<view class="rides">
					<image :src="itemData.params.nav[0].navimgUrl"></image>
				</view>
				<view class="rides_title">{{itemData.params.nav[0].title}}</view>
				<view class="rides_text">{{itemData.params.nav[0].text}}</view>
			</view>
			<view class="banner-xf-box" @click="gotoPage('/'+itemData.params.nav[1].navlinkUrl)">
				<view class="rides">
					<image :src="itemData.params.nav[1].navimgUrl" ></image>
				</view>
				<view class="rides_title">{{itemData.params.nav[1].title}}</view>
				<view class="rides_text">{{itemData.params.nav[1].text}}</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				indicatorDots: true,
				autoplay: true,
				interval: 2000,
				duration: 500,
				indicatorActiveColor: '#ffffff'
			}
		},
		props: ['itemData'],
		created() {
			this.interval = this.itemData.params.interval;
			this.indicatorActiveColor = this.itemData.style.btnColor;
		},
		methods: {

			/*跳转页面*/
			gotoPages(e) {
				this.gotoPage(e.linkUrl);
			}
		}
	}
</script>

<style lang="scss">
	.diy-banner-box,
	.diy-banner-box .swiper {
		width: 750rpx;
		height: 500rpx;
	}

	.diy-banner-box image {
		width: 750rpx;
		height: 500rpx;
	}

	.banner-xf-box {
		// flex: 1;
		display: flex;
		justify-content: center;
		flex-direction: column;
		align-items: center;
		width: 330rpx;
		height: 266rpx;
		border-radius: 30rpx;
		background-color: $dominant-color;
	}

	.diy-banner-xf {
		width: 690rpx;
		margin: 0 auto;
		position: relative;
		margin-top: -80rpx;
	}

	.rides {
		width: 85rpx;
		height: 85rpx;
		margin-bottom: 28rpx;

		image {
			width: 85rpx;
			height: 85rpx;
		}
	}

	.rides_title {
		color: #ffffff;
		font-weight: 600;
		font-size: 34rpx;

	}

	.rides_text {
		color: #ffffff;
		font-weight: 400;
		font-size: 20rpx;
	}
</style>
