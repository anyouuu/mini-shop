<template>
	<view class="diy-notice" :style="{ padding: itemData.style.paddingTop + 'px' + ' 15px', background: itemData.style.background }" @click="gotoPages(item)">
		<view class="notice-icon"><image :src="itemData.params.icon" mode="aspectFill"></image></view>
		<view class="notice-text flex-1 text-ellipsisss">
			<text :style="{ color: itemData.style.textColor }">{{ textData }}</text>
		</view>
	</view>
</template>

<script>
export default {
	data() {
		return {
			textData:''
		};
	},
	props: ['itemData'],
	created() {
		this.textData=this.itemData.params.text;
		this.horseRaceLamp();
	},
	methods: {
		
		horseRaceLamp(){
			let self=this;
			setTimeout(function(){
				let firstWord=self.textData.slice(0,1);
				let afterWord=self.textData.slice(1,self.textData.length);
				self.textData=afterWord+firstWord;
				self.horseRaceLamp();
				
			},200);
		},
		
		/*跳转页面*/
		gotoPages(e) {
			this.gotoPage(e.linkUrl);
		}
	}
};
</script>

<style>
.diy-notice {
	margin: 20rpx;
	display: flex;
	justify-content: flex-start;
	align-items: center;
	border-radius: 12rpx;
}
.diy-notice .notice-icon {
	width: 32rpx;
	height: 32rpx;
}
.diy-notice .notice-icon image {
	width: 100%;
	height: 100%;
}
.diy-notice .notice-text {
	overflow: hidden;
	white-space: nowrap;
	margin-left: 10px;
	font-size: 32rpx;
}
</style>
