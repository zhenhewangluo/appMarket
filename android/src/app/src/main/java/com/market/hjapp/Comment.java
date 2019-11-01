
package com.market.hjapp;

public class Comment {
	
	private String mUid;
	private String mName;
	private String mTime;
	private String mRate;
	private String mContent;
	
	@Override
	public String toString() {
		StringBuilder sb = new StringBuilder();
		sb.append("\nmUid : ").append(mUid)
				.append("\nmName : ").append(mName)
				.append("\nmTime : ").append(mTime)
				.append("\nmRate : ").append(mRate)
				.append("\nmContent : ").append(mContent).append("\n");
		return sb.toString();
	}
	
	public Comment() {
		this("", "", "", "", "");
	}
	
	public Comment(String uid, String name, String time, String rate, String content) {
		
		mUid = uid;
		mName = name;
		mTime = time;
		mRate = rate;
		mContent = content;
	}
	
	
	public void setUid(String uid) {
		mUid = uid;
	}
	
	public void setName(String name) {
		mName = name;
	}
	
	public void setTime(String time) {
		mTime = time;
	}
	
	public void setRate(String rate) {
		mRate = rate;
	}
	
	public void setContent(String content) {
		mContent = content;
	}
	
	
	public String getUid() {
		return mUid;
	}
	
	public String getName() {
		return mName;
	}
	
	public String getTime() {
		return mTime;
	}
	
	public String getRate() {
		return mRate;
	}
	
	public String getContent() {
		return mContent;
	}
}
