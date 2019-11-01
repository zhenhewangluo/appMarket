
package com.market.hjapp;

public class ChargeChannel {
	private String mID;
	private String mName;
	private String mIconUrl;
	
	
	public ChargeChannel() {
	}
	
	
	public void setId(String id) {
		mID = id;
	}
	
	public void setName(String name) {
		mName = name;
	}
	
	public void setIconUrl(String url) {
		mIconUrl = url;
	}
	
	public String getId() {
		return mID;
	}
	
	public String getName() {
		return mName;
	}
	
	public String getIconUrl() {
		return mIconUrl;
	}
}
