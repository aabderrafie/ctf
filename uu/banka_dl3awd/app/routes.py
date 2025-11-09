from flask import render_template, redirect, request, url_for, flash
import os
from flask_login import login_user, logout_user, login_required, current_user
from werkzeug.security import generate_password_hash, check_password_hash
from app import app, db, login_manager
from app.models import User, Transfer

@login_manager.user_loader
def load_user(user_id):
    return User.query.get(int(user_id))

@app.route("/")
def index():
    return render_template("index.html")

@app.route("/register", methods=["GET", "POST"])
def register():
    if request.method == "POST":
        username = request.form["username"]
        password = generate_password_hash(request.form["password"])
        if User.query.filter_by(username=username).first():
            flash("Username already exists")
            return redirect("/register")
        db.session.add(User(username=username, password=password))
        db.session.commit()
        return redirect("/login")
    return render_template("register.html")

@app.route("/login", methods=["GET", "POST"])
def login():
    if request.method == "POST":
        user = User.query.filter_by(username=request.form["username"]).first()
        if user and check_password_hash(user.password, request.form["password"]):
            login_user(user)
            return redirect("/dashboard")
        flash("Invalid credentials")
    return render_template("login.html")

@app.route("/logout")
@login_required
def logout():
    logout_user()
    return redirect("/")

@app.route("/dashboard")
@login_required
def dashboard():
    transfers = Transfer.query.filter_by(receiver_id=current_user.id, status="pending").all()
    return render_template("dashboard.html", user=current_user, transfers=transfers)

@app.route("/buyflag", methods=["GET"])
@login_required
def buyflag():
    if (current_user.balance < -100000.0):
        return (f"Flag {os.getenv('FLAG')} {current_user.balance}")
    else :
        return (f"Insufficient funds {current_user.balance}")

@app.route("/transfer", methods=["GET", "POST"])
@login_required
def transfer():
    if request.method == "POST":
        recipient = User.query.filter_by(username=request.form["to"]).first()
        amount = float(request.form["amount"])
        if not recipient or recipient.id == current_user.id:
            flash("Invalid recipient")
        elif current_user.balance < amount:
            flash("Insufficient funds")
        else:
            t = Transfer(sender_id=current_user.id, receiver_id=recipient.id, amount=amount, status="pending")
            db.session.add(t)
            db.session.commit()
            flash("Transfer initiated")
            return redirect("/dashboard")
    return render_template("transfers.html")

@app.route("/transfer/<int:transfer_id>/<action>")
@login_required
def handle_transfer(transfer_id, action):
    t = Transfer.query.get(transfer_id)
    if t and t.receiver_id == current_user.id and t.status == "pending":
        if action == "accept":
            sender = User.query.get(t.sender_id)
            receiver = User.query.get(t.receiver_id)
            sender.balance -= t.amount
            receiver.balance += t.amount
            t.status = "accepted"
        elif action == "refuse":
            t.status = "refused"
        db.session.commit()
    return redirect("/dashboard")
