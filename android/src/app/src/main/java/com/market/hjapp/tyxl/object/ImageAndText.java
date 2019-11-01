package com.market.hjapp.tyxl.object;

public class ImageAndText {
	private String imageUrl;
	// 奖品Id
	public String giftId;
	// 奖品名称
	public String giftName;
	// 列表图标
	// public Bitmap Picurl;
	// 积分
	public String Score;
	// URL地址
	public String URL;
	// 总数量
	public String oldnum;
	// 剩余数量
	public String newnum;

	public ImageAndText(String imageUrl, String giftId, String giftName,
			String Score, String URL, String oldnum, String newnum) {
		this.imageUrl = imageUrl;
		this.giftId = giftId;
		this.giftName = giftName;
		this.Score = Score;
		this.URL = URL;
		this.oldnum = oldnum;
		this.newnum = newnum;
	}

	public String getImageUrl() {
		return imageUrl;
	}

	public String getGiftId() {
		return giftId;
	}
	public String getGiftName() {
		return giftName;
	}
	public String getScore() {
		return Score;
	}
	public String getURL() {
		return URL;
	}
	public String getOldnum() {
		return oldnum;
	}
	public String getNewnum() {
		return newnum;
	}
}
