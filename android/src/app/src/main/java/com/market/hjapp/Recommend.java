
package com.market.hjapp;

public class Recommend {
	private int mId;
	private String mName;
	private String mDesc;
	private String mIconUrl;
	private String mImageUrl;
	private String mDate;
	private int mTargetType;
	private String mTargetId;
	private String mRecDesc;
	
    public static final int TARGET_TYPE_APP        = 0;
	
	public void setId(int id) {
		mId = id;
	}
	
	public int getId() {
		return mId;
	}
	
	public void setName(String name) {
		mName = name;
	}
	
	public String getName() {
		return mName;
	}
	
	public void setDesc(String desc) {
		mDesc = desc;
	}
	
	public String getDesc() {
		return mDesc;
	}
	
	public void setIconUrl(String iconUrl) {
		mIconUrl = iconUrl;
	}
	
	public String getIconUrl() {
		return mIconUrl;
	}
	
	
	public void setImageUrl(String imageUrl) {
		mImageUrl = imageUrl;
	}
	
	public String getImageUrl() {
		return mImageUrl;
	}
	
	public void setDate(String date) {
		mDate = date;
	}
	
	public String getDate() {
		return mDate;
	}
	
	public void setTargetType(int targetType) {
		mTargetType = targetType;
	}
	
	public int getTargetType() {
		return mTargetType;
	}
	
	public void setTargetId(String targetId) {
		mTargetId = targetId;
	}
	
	public String getTargetId() {
		return mTargetId;
	}

	public String getRecDesc() {
		return mRecDesc;
	}

	public void setRecDesc(String mRecDesc) {
		this.mRecDesc = mRecDesc;
	}

	
}
