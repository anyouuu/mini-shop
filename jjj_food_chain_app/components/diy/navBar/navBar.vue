<template>
	<view>
		<!--图标入口-->
		<view class="diy-navbar">
			<template v-if="itemData.style.rowsNum == 1">
				<view class="item rowsNum1" v-for="(item, index) in itemData.data" :key="index"
					@click="gotoDetail(item)">
					<view class="hh100 d-c-s d-c flex-1">
						<view class="d-s-c mb10">
							<view class="gray3 title flex-1">{{item.title}}</view>
							<view class="icon iconfont icon-jiantou"></view>
						</view>
						<text class="gray9 text" :style="{color:item.color}">{{item.text}}</text>
					</view>
					<view class="nav-image">
						<image :src="item.imageUrl" mode=""></image>
					</view>
				</view>
			</template>
			<template v-if="itemData.style.rowsNum == 2">
				<view class="item rowsNum2" v-for="(item, index) in itemData.data" :key="index"
					@click="gotoDetail(item)">
					<view class="nav-image">
						<image :src="item.imageUrl" mode=""></image>
					</view>
					<view class="d-s-c">
						<view class="gray3 title flex-1">{{item.title}}</view>
					</view>
					<view class="gray9 text" :style="{color:item.color}">{{item.text}}</view>
				</view>
			</template>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				//单个宽度
				item_width: '',
				item_height: ''
			}
		},
		props: ['itemData'],
		created() {
			this.item_width = this.itemData.style.rowsNum == 1 ? '690rpx' : '330rpx'
			this.item_height = this.itemData.style.rowsNum == 1 ? '150rpx' : '254rpx'
		},
		methods: {

			/*跳转页面*/
			gotoDetail(e) {
				this.gotoPage(e.linkUrl);
			}
		}
	}
</script>

<style lang="scss">
	.diy-navbar {
		padding: 20rpx 30rpx;
		display: flex;
		justify-content: space-between;
		flex-wrap: wrap;
	}

	.diy-navbar .item {
		display: flex;
		box-sizing: border-box;
		padding: 20rpx;
		box-sizing: border-box;
		box-shadow: 0px 3rpx 6rpx rgba(0, 0, 0, 0.16);
		border-radius: 18rpx;
	}
	.diy-navbar .item.rowsNum1{
		
		justify-content: flex-start;
		align-items: flex-start;
		width: 690rpx;
		height: 150rpx;
	}
	.diy-navbar .item.rowsNum2{
		flex-direction: column;
		justify-content: center;
		align-items: center;
		width: 330rpx;
		height: 254rpx;
	}
	.diy-navbar .item image {
		width: 100rpx;
		height: 100rpx;
	}

	.diy-navbar .item text {
		display: block;
		text-overflow: ellipsis;
		white-space: nowrap;
		width: 100%;
		overflow: hidden;
		font-size: 22rpx;
	}

	.diy-navbar .item .title {
		font-weight: 600;
		font-size: 30rpx;
		color: #333333;
	}

	.diy-navbar .item .icon-jiantou {
		color: $dominant-color;
		font-size: 26rpx;
		margin-left: 12rpx;
	}
	.nav-image{
		width: 100rpx;
		height: 100rpx;
		image{
			width: 100rpx;
			height: 100rpx;
		}
	}
	.item.rowsNum1 .nav-image{
		
	}
	.item.rowsNum2 .nav-image{
		margin-bottom: 18rpx;
	}
</style>
