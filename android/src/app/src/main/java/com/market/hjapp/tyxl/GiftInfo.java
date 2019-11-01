package com.market.hjapp.tyxl;

import java.io.Serializable;

import android.graphics.Bitmap;

public class GiftInfo implements Serializable {

	public Bitmap GifImg;
	public String GifId;
	public String GifScore;

	public Bitmap getGifImg() {
		return GifImg;
	}

	public void setGifImg(Bitmap GifImg) {
		this.GifImg = GifImg;
	}

	public String getGifId() {
		return GifId;
	}

	public void setGifId(String GifId) {
		this.GifId = GifId;
	}

	public String getGifScore() {
		return GifScore;
	}

	public void setGifScore(String GifScore) {
		this.GifScore = GifScore;
	}



}
