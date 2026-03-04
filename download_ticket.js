async function downloadTicket() {
    try {
        const res = await fetch('fetch_ticket.php');
        const t = await res.json();

        if(t.error){
            alert(t.error);
            return;
        }

        // Fill HTML ticket
        document.getElementById('event_name').innerText = t.event_name;
        document.getElementById('full_name').innerText = t.full_name;
        document.getElementById('email').innerText = t.email;
        document.getElementById('phone').innerText = t.phone;
        document.getElementById('ticket_code').innerText = t.ticket_code;
        document.getElementById('event_date').innerText = t.event_date;

        // Capture ticket div
        const ticketElement = document.getElementById('ticket');
        const canvas = await html2canvas(ticketElement, { scale: 2 }); // higher res

        // Convert canvas to image
        const imgData = canvas.toDataURL("image/png");

        // Create a temporary link to download
        const link = document.createElement('a');
        link.href = imgData;
        link.download = "ticket.png";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Redirect after download
        window.location.href = "landingpage.html";

    } catch (error) {
        console.error(error);
        alert("Failed to download ticket.");
    }
}