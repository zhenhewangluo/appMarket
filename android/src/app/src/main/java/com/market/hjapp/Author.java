/*
 * COPYRIGHT AND PERMISSION NOTICE
 * 
 * 
 * Permission is NOT granted to any person obtaining a copy of this 
 * software and associated documentation files (the "Software"), to 
 * deal in the Software, including the rights to use, copy, modify, 
 * merge, publish, distribute, and/or sell copies of the Software, and 
 * this permission notice appear in all copies of the Software and that
 * both the above copyright notice(s) and this permission notice appear
 * in supporting documentation.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, 
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT 
 * OF THIRD PARTY RIGHTS. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR 
 * HOLDERS INCLUDED IN THIS NOTICE BE LIABLE FOR ANY CLAIM, OR ANY 
 * SPECIAL INDIRECT OR CONSEQUENTIAL DAMAGES, OR ANY DAMAGES WHATSOEVER 
 * RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF 
 * CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF OR IN 
 * CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 * 
 * Except as contained in this notice, the name of a copyright holder 
 * shall not be used in advertising or otherwise to promote the sale, 
 * use or other dealings in this Software without prior written 
 * authorization of the copyright holder.
 *
 */

package com.market.hjapp;

import java.util.ArrayList;

public class Author {
	private String mAid;
	private String mName;
	private String mUrl;
	private String mEmail;
	private String mPhone;
	private ArrayList<App> mApp;
	
	@Override
	public String toString() {
		StringBuilder sb = new StringBuilder();
		sb.append("mAid : ").append(mAid)
				.append("\nmName : ").append(mName)
				.append("\nmUrl : ").append(mUrl)
				.append("\nmEmail : ").append(mEmail)
				.append("\nmPhone : ").append(mPhone);
		return sb.toString();
	}
	
	public Author() {
		this("", "", "", "", "", new ArrayList<App>());
	}
	
	public Author(String aid, String name, String url, String email, String phone, ArrayList<App> app) {
		mAid = aid;
		mName = name;
		mUrl = url;
		mEmail = email;
		mPhone = phone;
		mApp = app;
	}
	
	public void setAid(String aid) {
		mAid = aid;
	}
	
	public void setName(String name) {
		mName = name;
	}

	public void setUrl(String url) {
		mUrl = url;
	}

	public void setEmail(String email) {
		mEmail = email;
	}

	public void setPhone(String phone) {
		mPhone = phone;
	}

	public void setApp(ArrayList<App> app) {
		mApp = app;
	}
	
	public String getAid() {
		return mAid;
	}
	
	public String getName() {
		return mName;
	}
	
	public String getUrl() {
		return mUrl;
	}
	
	public String getEmail() {
		return mEmail;
	}
	
	public String getPhone() {
		return mPhone;
	}
	
	public ArrayList<App> getApp() {
		return mApp;
	}
}
